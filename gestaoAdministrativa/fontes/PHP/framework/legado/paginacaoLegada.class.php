<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/
?>

<?php
/**************************************************************************/
/**** Declara as variaveis utilizadas na classe                         ***/
/**************************************************************************/
class paginacaoLegada
{
    public $sql;
    public $pagina;
    public $rpp;
    public $quantidade;
    public $paginas;
    public $inicio;
    public $aux;
    public $order;
    public $tipo;
    public $uni;
    public $complemento;
/**************************************************************************/
/**** Método Construtor                                                 ***/
/**************************************************************************/

    public function paginacaoLegada()
    {
        $this->sql = "";
        $this->pagina = "";
        $this->rpp = "";
        $this->quantidade = "";
        $this->paginas = "";
        $this->inicio = "";
        $this->aux = "";
        $this->order  = "";
        $this->tipo = "";
        $this->uni = "";
    }

/**************************************************************************/
/**** Pega os dados disponibilizados de query e registros po pagina     ***/
/**************************************************************************/
    public function pegaDados($sql, $rpp, $uni="")
    {
        $this->sql = $sql;
        $this->rpp = $rpp;
        $this->uni = $uni;
    }
/**************************************************************************/
/**** Pega o número  da página atual                                    ***/
/**************************************************************************/
     function pegaPagina($pagina)
     {
        $this->pagina = $pagina;
    }

/**************************************************************************/
/**** Gera os links de navegação entre  os registros                    ***/
/**************************************************************************/
    public function geraLinks()
    {
        global $PHP_SELF;

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($this->sql);
        $this->quantidade  = $dbEmp->numeroDeLinhas;
        $this->paginas = ceil($this->quantidade / $this->rpp);
        $this->inicio = $this->pagina * $this->rpp;

        if ($this->uni == 1) {
            if ($this->pagina == "")
            $this->inicio = 0;
            if ($this->pagina == 0)
            $this->inicio = 0;
            if ($this->pagina == 1)
            $this->inicio = 1;
            if ($this->pagina == 2)
            $this->inicio = 2;
            if ($this->pagina >= 3)
            $this->inicio = $this->pagina;
        }

        if ($this->quantidade > $this->rpp) {
            if ($this->pagina > 0) {
                $url = "$PHP_SELF?".Sessao::getId()."&pagina=".($this->pagina - 1);
                $url_primeiro = "$PHP_SELF?".Sessao::getId()."&pagina=1";
        
                if ($this->complemento) {
                   $url .= $this->complemento;
                   $url_primeiro.= $this->complemento;
                }
        
                $this->aux = "<li><a href=".$url_primeiro.">Primeiro</a></li>";
                $this->aux.= "<li><a href=".$url.">Anterior</a></li>";
            }
            
            // Calculo para montar paginação
            $indiceX=0;
            $limiteY=10;
            if($this->paginas > 11) {
                if($this->pagina < 5) {
                    $indiceX=0;
                    $limiteY=10;
                } else {
                    $indiceX = $this->pagina - 4;
                    $limiteY=10+$indiceX;
                    
                    if($limiteY > $this->paginas) {
                        $limiteY=$this->paginas;
                        
                        if (($indiceX < 10) || (($limiteY-$indiceX) < 10)) {
                            $indiceX=$limiteY-10;
                        }
                    }
                }
            }
            
            for ($i=$indiceX; $i < $limiteY; $i++) {
                $url = "$PHP_SELF?".Sessao::getId()."&pagina=$i";
                if ($this->complemento) {
                    $url .= $this->complemento;
                }
                $j = $i+1;
                if ($this->pagina == $i) {
                    $this->aux .= "<li class='active'><a href=".$url.">".$j."</a></li>";
                } else {
                    $this->aux .= "<li><a href=".$url.">$j</a></li>";
                }
            }
            
            if ($this->pagina < ($this->paginas - 1)) {
                $url = "$PHP_SELF?".Sessao::getId()."&pagina=".($this->pagina+1);
                $url_ultimo = "$PHP_SELF?".Sessao::getId()."&pagina=".($this->paginas-1);
                
                if ($this->complemento) {
                   $url .= $this->complemento;
                   $url_ultimo .= $this->complemento;
                }
                $this->aux .= "<li><a href=".$url.">Próximo</a></li>";
                $this->aux .= "<li><a href=".$url_ultimo.">Último</a></li>";
            }
        }
    }

/**************************************************************************/
/**** Gera os links de navegação entre  os registros utilizando uma      **/
/**** função javascript definida pelo usuário                            **/
/**************************************************************************/
    public function geraLinksFuncao($funcao)
    {
        global $PHP_SELF;

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($this->sql);
        $this->quantidade  = $dbEmp->numeroDeLinhas;
        $this->paginas = ceil($this->quantidade / $this->rpp);
        $this->inicio = $this->pagina * $this->rpp;
        if ($this->uni == 1) {
            if ($this->pagina == "")
                $this->inicio = 0;
            else
                $this->inicio = $this->pagina;
        }
        if ($this->quantidade > $this->rpp) {
            if ($this->pagina > 0) {
                $menos = $this->pagina - 1;
                $url = "pagina=$menos";
                if ($this->complemento) {
                    $url .= $this->complemento;
                }
               $url = $funcao."(\"".$url."\")";
               $this->aux = "<li><a href='#' onClick='javascript:".$url."'>Anterior</a></li>";
            }
            for ($i=0;$i<$this->paginas;$i++) {
                $url = "pagina=$i";
                if ($this->complemento) {
                    $url .= $this->complemento;
                }
                $url = $funcao."(\"".$url."\")";
                $j = $i+1;

                if ($this->pagina == $i) {
                    $this->aux .= " <li class='active'><a href='#' onClick='javascript:".$url."'>".$j."</a></li>";
                } else {
                    $this->aux .= " <li><a href='#' onClick='javascript:".$url."'>$j</a></li>";
                }
            }
            if ($this->pagina < ($this->paginas - 1)) {
                $mais = $this->pagina + 1;
                //$url = "$PHP_SELF?Sessao::getId()&pagina=$mais";
                $url = "pagina=$mais";
                if ($this->complemento) {
                   $url .= $this->complemento;
                }
                $url = $funcao."(\"".$url."\")";
                $this->aux .= " <li><a href='#' onClick='javascript:".$url."'>Próxima</a></li>";
            }
        }
    }

/**************************************************************************/
/**** Pega o campo para order                                           ***/
/**************************************************************************/
    function pegaOrder($order,$tipo)
    {
        $this->order = $order;
        $this->tipo = $tipo;
    }

/**************************************************************************/
/**** Gera a quey completa para uso da paginação                       ***/
/**************************************************************************/
    public function geraSQL()
    {
        $query = $this->sql;
        $query .= " ORDER by $this->order $this->tipo";

        if ($this->uni != 1) {
            if (($this->inicio == 1) || ($this->inicio < 0)) {
                $this->inicio = 0;
            }
        }

        $query .= " LIMIT $this->rpp OFFSET $this->inicio";

        return $query;
    }

/**************************************************************************/
/**** Escreve os links na página                                        ***/
/**************************************************************************/
    public function mostraLinks()
    {
        echo "<ul class='pagination'>";
        echo "$this->aux";
        echo "</ul>";
    }

/**************************************************************************/
/**** Retorna o numero do primeiro registro da pagina corrente          ***/
/**************************************************************************/
     function contador()
     {
        if ($this->pagina) {
           return ($this->pagina * $this->rpp) +1;
        } else {
            return 1;
        }
    }
}

?>
