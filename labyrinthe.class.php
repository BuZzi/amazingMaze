<?php

/**
* Classe qui g�n�re un labyrinthe et qui le r�sout
*/
class labyrinthe
{
		// Propri�t�s
		protected $i_L; // L represente la largeur 
		protected $i_H; // H represente la hauteur
		protected $i_Tcase; // Taille des cases 
		
		/**
		* Constructeur
		* @param $i_Largeur : Largeur du labyrinthe
		* @param $i_Hauteur : Hauteur du labyrinthe
		* @param $i_TailleCase : Taille des cases
		**/
		public function __construct($i_Largeur, $i_Hauteur, $i_TailleCase = 20)
		{
			$this->i_L = $i_Largeur;
			$this->i_H = $i_Hauteur;
			$this->i_Tcase = $i_TailleCase;
		}
		
		/**
		* Fonction qui g�n�re un labyrinthe parfait avec la m�thode dite de fusion.
		* @return $a_Labyrinthe : un tableau repr�sentant le labyrinthe final � afficher
		**/
		public function generer()
		{
			// On r�cup�re la largeur et hauteur du labyrinthe voulu
			$l = $this->i_L;
			$h = $this->i_H;
			
			// On cr�� un tableau qui sera le labyrinthe final
			$a_Labyrinthe = array();
			
			$k = 0;
			// On boucle pour cr�er des tableaux (qui r�pr�sentent les cases) en fonction des largeurs et hauteurs voulues   
			for($i = 0; $i < $h; $i++)
			{
				$a_Labyrinthe[$i] = array();
				for($j = 0; $j < $l; $j++)
				{
					$a_Labyrinthe[$i][$j] = array();
					$a_Labyrinthe[$i][$j][S] = 0;
					$a_Labyrinthe[$i][$j][E] = 0;
					$a_Labyrinthe[$i][$j][IDX] = $k;
					$k++;
				}
			}
			$k--; // On r�ajuste
			$i = 0;
			$nb_cloison = ($l*$h)-1;
		
			$a_Murs = array();
			
			for($i = 0; $i < $k; $i++)
			{
				if($i < ($l*($h-1)) )
				{
					$a_Murs[S][] = $i;
				}
				if(($i+1)%$l)
				{
					// La case est ouvrable vers la droite
					$a_Murs[E][] = $i;
				}
			}
			
			$a_directions = array(S, E);
					
			$i = 0;
			// Tant qu'on a pas parcouru toutes les cloisons (murs)
			while($i < $nb_cloison)
			{
				do
				{
					/*	
					On prend n'importe quelle cellules (sauf la derni�re qu'on ne peut pas ouvrir)
					On peut fusionner dans 2 directions Sud et Est.
					On en prend une au hasard, si c'est impossible on prend l'autre, sinon on prend une autre case.
					*/
					
					// On prend une direction au hasard
					$dir = $a_directions[array_rand($a_directions)];
					
					// Une case que l'on peut ouvrir dans cette direction
					$cle = array_rand($a_Murs[$dir]);
					$id = $a_Murs[$dir][$cle];
					unset($a_Murs[$dir][$cle]);
						
					if(empty($a_Murs[$dir]))		
						unset($a_directions[array_search($dir, $a_directions)]);
							
					$x1 = $this->getX($id, $l);
					$y1 = $this->getY($id, $l);
					
					if($dir == S)
						$id2 = $id + $l;
					else
						$id2 = $id +1;
						
						$x2 = $this->getX($id2, $l);
						$y2 = $this->getY($id2, $l);
					
						
						$idx1 = $a_Labyrinthe[$y1][$x1][IDX];
						$idx2 = $a_Labyrinthe[$y2][$x2][IDX];
						
				// Tant que l'ouverture rejoint deux fois le m�me groupe de cellules
				}while($idx1 == $idx2);
				
				// On ouvre les murs
				$a_Labyrinthe[$y1][$x1][$dir] = true;
				
				// On met les deux cellules au m�me index
				for($j = 0; $j < $h; $j++)
				{
					for($k = 0; $k < $l; $k++)
					{
						if($a_Labyrinthe[$j][$k][IDX] == $idx2)
						{
							$a_Labyrinthe[$j][$k][IDX] = $idx1;
						}
					}
				}
				$i++;
			}
			// On retourne le labyrinthe final
			return $a_Labyrinthe;
		} // FIN public function generer()
		
		/** 
		* Fonction qui affiche un labyrinthe en HTML.
		* @param $a_Labyrinthe : un tableau qui contient les cases g�n�r�es pr�c�d�mment
		* @param $solution : la solution du labyrinthe (resolveur.class.php)
		**/
		public function laby2html($a_Labyrinthe , $solution = null)
		{			
			$l = $this->i_L;
			$h = $this->i_H;
			
			global $LARGEUR_CASE;
			static $etape = 1;
			$width = 50*$l + 6;		
					
			echo '<div class="bloc">';
			echo '<div class="bloc_tableau">';
			echo '<table>';
			$i = 0;
			foreach($a_Labyrinthe as $y => $ligne)
			{
				echo '<tr>';
				foreach($ligne as $x => $case)
				{
					echo '<td class="';
					if($case[S] == 1)
						echo 'blc_bas ';
					
					if($case[E] == 1)
						echo 'blc_est ';
					if($y == 0 && $x == 0)
						echo 'depart';
					elseif($y == $h-1 && $x == $l-1)
						echo 'arrivee';
						
					// Si on demande la solution (qu'elle est pass�e en param�tre de la fonction)
					if(isset($solution) && in_array($i, $solution))
						// les cases qui constituent le chemin de r�solution se voient affecter une couleur de fond diff�rente
						echo ' chemin ';
						echo '"><!-- case --></td>'."\n"; // \n pour la clart� du code html g�n�r�
					$i++;
				}
				echo '</tr>';
			}
			echo '</table>
				  </div>
				  </div>';
					
			$etape++;			
		}
	
	/**
	* Fonction qui renvoi les coordonn�es en X � partir de l'index
	**/
	protected function getX($idx, $l)
	{
		return intval($idx % $l);	
	}
	
	/**
	* Fonction qui renvoi les coordonn�es en Y � partir de l'index
	**/
	protected function getY($idx, $l)
	{
		return intval($idx / $l);	
	}
	
	/**
	* Fonction qui renvoi l'index a partir des coordonn�es
	**/
	protected function coord2idx($x, $y, $l)
	{
		return intval($l * $y + $x);	
	}	
}
?>
