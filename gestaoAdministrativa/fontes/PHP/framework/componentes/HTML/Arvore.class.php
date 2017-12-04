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
* Componente para visualizar os dados em forma de árvore
* Data de Criação: 16/09/2004

* @author Desenvolvedor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML da árvore

    * @package framework
    * @subpackage componentes
*/
class Arvore extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stName;
/**
    * @access Private
    * @var String
*/
var $stNameReduzido;
/**
    * @access Private
    * @var String
*/
var $stValue;
/**
    * @access Private
    * @var String
*/
var $stLink;
/**
    * @access Private
    * @var String
*/
var $stRotulo;
/**
    * @access Private
    * @var String
*/
var $stTitle;
/**
    * @access Private
    * @var String
*/
var $stDefinicao;
/**
    * @access Private
    * @var Boolean
*/
var $boNull;
/**
    * @access Private
    * @var Boolean
*/
var $boFolhasVisiveis;
/**
    * @access Private
    * @var Object
*/
var $rsRecordSet;
/**
    * @access Private
    * @var String
*/
var $stHtml;

/**
    * @access Public
    * @param String $valor
*/
function setName($valor) { $this->stName       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNameReduzido($valor) { $this->stNameReduzido = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValue($valor) { $this->stValue      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setLink($valor) { $this->stLink       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setRotulo($valor) { $this->stRotulo     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDefinicao($valor) { $this->stDefinicao  = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setNull($valor) { $this->boNull       = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setFolhasVisiveis($valor) { $this->boFolhasVisiveis= $valor; }
/**
    * @access Public
    * @param Object  $valor
*/
function setRecordSet($valor) { $this->rsRecordSet  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setHtml($valor) { $this->stHtml       = $valor; }

/**
    * @access Public
    * @return String
*/
function getName() { return $this->stName;       }
/**
    * @access Public
    * @return String
*/
function getNameReduzido() { return $this->stNameReduzido; }
/**
    * @access Public
    * @return String
*/
function getValue() { return $this->stValue;      }
/**
    * @access Public
    * @return String
*/
function getLink() { return $this->stLink;       }
/**
    * @access Public
    * @return String
*/
function getRotulo() { return $this->stRotulo;     }
/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;      }
/**
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao;  }
/**
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull;       }
/**
    * @access Public
    * @return Boolean
*/
function getFolhasVisiveis() { return $this->boFolhasVisiveis;}
/**
    * @access Public
    * @return RecordSet
*/
function getRecordSet() { return $this->rsRecordSet;  }
/**
    * @access Public
    * @return String
*/
function getHtml() { return $this->stHtml;       }

/**
    * Método Construtor
    * @access Private
*/
function Arvore()
{
    $this->setFolhasVisiveis( true );
    $this->setNull      ( true );
    $this->setName      ( 'Arvore' );
    $this->setDefinicao ( 'Arvore' );
    $this->setRecordSet ( new RecordSet );
}
/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stElemento
    * @return Integer
*/
function verificaFilhosNulos($stElemento)
{
      $cont=0;
      $arrStElemento = explode(".",$stElemento);
      while (list($key,$val) = each($arrStElemento)) {
        if (intval($val)==0) {
            $cont++;
        }
      }
      if ($arrStElemento[0]<>0) {
          return $cont;
      } else {
        return 0;
      }
}
/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stElemento
    * @return Boolean
*/
function verificaFilhos($stElemento)
{
    $boOk = true;
    $arrStElemento = explode(".",$stElemento);
    while (list($key,$val) = each($arrStElemento)) {
        if (intval($val)==0) {
            $boOk = false;
        }
    }
    if ($boOk) {
        // Testa a versão do PHP e clona o objeto, se necessário.
        $rsRecordSet = clone $this->rsRecordSet;
        $rsRecordSet->setCorrente( $this->rsRecordSet->getCorrente()+1 );
        while ( !$rsRecordSet->eof() ) {
            $stCampo = $rsRecordSet->getCampo( $this->stNameReduzido );
            if(substr($stCampo,0,strlen($stElemento))==substr($stElemento,0,strlen($stElemento)))

                return true;
            $rsRecordSet->proximo();
        }
    }

    return false;
}
/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stElemento
    * @return Boolean
*/
function verificaPai($stElemento)
{
    $rsRecordSet = $this->rsRecordSet;
    $rsRecordSet->setCorrente( $this->rsRecordSet->getCorrente()-1 );
    while ( !$rsRecordSet->bof() ) {
        $stCampo = $rsRecordSet->getCampo( $this->stNameReduzido );

        if (substr($stCampo,0,strlen($stElemento))!=substr($stElemento,0,strlen($stElemento))) {
            return false;
        }
        $rsRecordSet->anterior();
    }

    return true;
}
/**
    * Monta o HTML do Objeto Arvore
    * @access Protected
*/
function montaHTML()
{
    $this->setNull      ( true );
    $stHtml  = '<table border=0><tr><td><font size=-2><a style="font-size:7pt;text-decoration:none;color:silver" href=http://www.treemenu.net/
    target=_blank></a></font></td></table> ';
    $stHtml .= "<script>\n";
    $stHtml .= " ETEXTLINKS = 0   \n";
    $stHtml .= " STARTALLOPEN = ".(($this->boFolhasVisiveis)?'1':'0')."   \n";
    $stHtml .= " USEFRAMES = 1      \n";
    $stHtml .= " USEICONS = 0       \n";
    $stHtml .= " WRAPTEXT = 1       \n";
    $stHtml .= " PRESERVESTATE = 0  \n";

    $arPais 	  = array();
    $arTodos	  = array();
    $arTodosNulos = array();

    if ( !$this->rsRecordSet->eof() ) {
        //Lista todos os orgãos do organograma
        while ( !$this->rsRecordSet->eof() ) {
            $inicio = 1;
            $stCampo = $this->rsRecordSet->getCampo( $this->stNameReduzido);
            $arCampoCompleto 	= preg_split("/[^a-zA-Z0-9]/", $stCampo);
            $arCampoReduzido	= preg_split("/[^a-zA-Z0-9]/", $this->rsRecordSet->getCampo( $this->stNameReduzido.'_reduzido' ) );
            $arCampoReduzidoPop = preg_split("/[^a-zA-Z0-9]/", $this->rsRecordSet->getCampo( $this->stNameReduzido.'_reduzido' ) );

            array_pop($arCampoReduzidoPop);

            $stCampo = $this->rsRecordSet->retornaValoresRecordSet( $this->stNameReduzido );
            $stValue = $this->rsRecordSet->retornaValoresRecordSet( $this->stValue);
            $stValue = "<NOBR>" . $stValue . "</NOBR>";

            //Verifica se o nível do orgão é o primeiro
            if ( $this->rsRecordSet->getCampo('nivel')==1 ) {
                 $stHtml .= 'aux'.implode('_',$arCampoReduzido).'_'.count($arCampoReduzido).' = gFld("<b>'.$stValue.'</b>", ""); '."\n";
                 $stHtml .= 'aux'.implode('_',$arCampoReduzido).'_'.count($arCampoReduzido).'.treeID = "t3"  '."\n";
                 $stHtml .= 'foldersTree = aux'.implode('_',$arCampoReduzido).'_'.count($arCampoReduzido).';  '."\n";
                 $arPais[] = implode('_',$arCampoReduzido) . "_" . count($arCampoReduzido);

                 $arTodos[implode('.',$arCampoReduzido)] = implode('_',$arCampoReduzido).'_'.count($arCampoReduzido);
            //Verifica se o orgão tem filhos e se o orgão não pulo de nível
            } elseif ( $this->verificaFilhos( implode('.',$arCampoReduzido) ) ) {
                $arPai = $arCampoReduzido;

                array_pop($arPai);

                //Posiciona o cursor no final do array
                end($arPai);
                //Verifica se o último indice é 0, enquanto for
                //será retirado este indice até restar o
                //código reduzido do pai
                while (current($arPai)==0) {
                    array_pop($arPai);
                    end($arPai);
                }

                $stHtml .= 'aux'.implode('_',$arCampoReduzido).'_'.count($arCampoReduzido).' = insFld(aux'.implode('_',$arPai) . '_' . count($arPai).', gFld("'.$stValue.'", "")); '."\n";
                $arPais[] = implode('_',$arCampoReduzido) . '_' . count($arCampoReduzido);

                $arTodos[implode('.',$arCampoReduzido)] = implode('_',$arCampoReduzido).'_'.count($arCampoReduzido);
            //Verifica se o orgão tem pulo de nível e retorna o número de pulos
            } elseif ($contador = ($this->verificaFilhosNulos(implode('.',$arCampoReduzido)))) {
                //Verifica se o orgão começa com níves pulados
                $cont = 0;
                while (list($key,$val) = each($arCampoReduzido)) {
                    if(intval($val)<>0)
                        $cont++;
                }

                //De acordo com a verificação acima ele seta a variavel
                //para que execute tarefas distintas (segue os códigos abaixo)
                if($cont<=2)
                    $boInicioZero = true;
                else
                    $boInicioZero = false;

                $arPai = $arCampoReduzido;
                array_pop($arPai);

                //Posiciona o cursor no final do array
                end($arPai);
                //Verifica se o último indice é 0, enquanto for
                //será retirado este indice até restar o
                //código reduzido do pai
                $boZero = false;
                while (current($arPai)==0) {
                    $boZero = true;
                    array_pop($arPai);
                    end($arPai);
                }

                //Posiciona o cursos no início do array
                //Este array contém todos os filhos que pulam de nível
                reset($arTodos);
                //Lista o código reduzido e o nome da variavel do orgão
                while (list($key,$val) = each($arTodos)) {
                    if ($boZero) {
                        $arContTodos = explode('.',$key);
                        array_pop($arContTodos);
                        $arContAtual = $arCampoReduzido;
                        $inUltimoValor = array_pop($arContAtual);
                        if ((count($arContTodos)==count($arContAtual)) && (implode('.',$arContTodos)==implode('.',$arContAtual))) {
                            $arIguais[implode('.',$arCampoReduzido).".".count($arCampoReduzido)] = implode('_',$arContAtual) . "_" . 1 . "_" . (count($arCampoReduzido)-1);
                        }
                    }

                    //Confere se algum orgão já listado é igual
                    //ao código do pai do registro atual
                    if ($key == implode('.',$arPai)) {
                        //Guarda o código do pai do orgão atual em forma de array
                        $stPai = $val;
                        //Faz os calculos de quantos níveis há de diferença
                        //entre o pai do orgão atual e o orgão atual
                        //Caso o if acima seja falso o valor do contador é de quantos pulos
                        //o orgão teve até encontrar seu primeiro valor
                        $contador = count($arCampoReduzido);
                        $inicio = count($arPai) + 1;
                    }
                }

                $arPais[] = implode('_',$arCampoReduzido) . "_" . count($arCampoReduzido);
                array_pop($arCampoReduzidoPop);

                //Se o orgão  iniciu pulado executa o código abaixo
                if ($boInicioZero) {
                    $boZ = true;
                } else {
                    $boZ = false;
                    //Verifica se o orgão tem pai ou se é filho de um registro de pulo
                    //Caso seja filho de um registro pulado o código será do ultimo registro fixo
                    //Caso exista o pai tera o código do pai
                    //caso não os nomes tera o o valor do inicio do array até o primeiro pulo(0)
                    if ($arIguais[implode('.',$arCampoReduzido).".".count($arCampoReduzido)]) {
                        $nomeAux = $arIguais[implode('.',$arCampoReduzido).".".count($arCampoReduzido)];
                    } elseif ($stPai) {
                        $nomeAux = $stPai;
                    } else {
                        $nomeAux = implode('_',$arCampoReduzido).'_'.count($arCampoReduzido);
                    }
                }

                if (!$boZ) {
                    //Faz um loop de acordo com o número de pulos necessários
                    for ($iCount=$inicio; $iCount<$contador; $iCount++) {
                        $stValueTMP = explode ("-", $stValue);
                        $stValueTMP = $stValueTMP[0];
                        $stValueTMP = preg_replace ("/[0-9]/","0",$stValueTMP);

                        //Insere no array de Todos o codigo reduzido e o nome da variavel do orgão
                        $arTodos[implode('.',$arCampoReduzido)] = implode('_',$arCampoReduzido).'_'.$iCount;

                        $stHtml .= 'aux'.implode('_',$arCampoReduzido).'_'.$iCount.' = insFld(aux'.$nomeAux.', gFld("'.$stValueTMP.'", "")); '."\n";

                        $nomeAux = implode('_',$arCampoReduzido).'_'.$iCount;
                    }

                    //Insere no array de Todos o codigo reduzido e o nome da variavel do orgão
                    $arTodos[implode('.',$arCampoReduzido)] = implode('_',$arCampoReduzido).'_'.count($arCampoReduzido);

                    $stHtml .= 'aux'.implode('_',$arCampoReduzido).'_'.count($arCampoReduzido).' = insFld(aux'.$nomeAux.', gFld("'.$stValue.'", "")); '."\n";
                } else {
                    $stNomeInicial = 'Z_1';
                    $nome2Aux = $stPai;
                    $nome3Aux = $stPai;
                    //Faz um loop de acordo com o número de pulos necessários
                    for ($iCount=$inicio; $iCount<$contador; $iCount++) {

                        $stValueTMP = explode ("-", $stValue);
                        $stValueTMP = $stValueTMP[0];
                        $stValueTMP = preg_replace ("/[0-9]/","0",$stValueTMP);

                        //Verifica se este registro nulo já foi criado, caso tenha sido ele ignora este código
                        if (!$arTodosNulos[$nome3Aux . $stNomeInicial]) {
                            $arTodosNulos[$nome3Aux . $stNomeInicial] = $nome2Aux;

                            $stHtml .= 'aux'.$nome3Aux . $stNomeInicial . ' = insFld(aux'.$nome2Aux.', gFld("'.$stValueTMP.'", "")); '."\n";

                        }

                            $nome2Aux = $stPai . 'Z_' . ($iCount-1);
                            $nome3Aux = $stPai . 'Z_' . $iCount;
                            $stNomeInicial = "";

                    }

                    //Insere no array de Todos o codigo reduzido e o nome da variavel do orgão
                    $arTodos[implode('.',$arCampoReduzido)] = implode('_',$arCampoReduzido).'_'.count($arCampoReduzido);

                    $stHtml .= 'aux'.implode('_',$arCampoReduzido).'_'.count($arCampoReduzido).' = insFld(aux'.$nome2Aux.', gFld("'.$stValue.'", "")); '."\n";
                }
            //Caso nenhuma das verificações acima não seja verdadeira o orgão é uma folha
            } else {
                $arPai = $arCampoReduzido;
                array_pop($arPai);

                //Posiciona o cursor no final do array
                end($arPai);
                //Verifica se o último indice é 0, enquanto for
                //será retirado este indice até restar o
                //código reduzido do pai
                while (current($arPai)==0) {
                    array_pop($arPai);
                    end($arPai);
                }

                $stHtml .= 'insDoc(aux'.implode('_',$arPai) . '_' . count($arPai).', gLnk("S", "'.$stValue.'", "")); '."\n";
                $stPai = "";
            }
            $this->rsRecordSet->proximo();
        }
    }
    $stHtml .= "</script>\n";
    $stHtml .= "<span class=TreeviewSpanArea><script>initializeDocument()</script></span>\n";

    $obTabela = new Tabela;
    $obTabela->setBorder('0');
    $obTabela->addLinha();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth      ( 100 );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo   ( $stHtml );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHtml( $obTabela->getHtml() );
}

/**
    * Imprime o HTML do Objeto Label na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHtml();
    $stHtml = $this->getHtml();
    $stHtml =  trim( $stHtml )."\n";
    echo $stHtml;
}

}

?>
