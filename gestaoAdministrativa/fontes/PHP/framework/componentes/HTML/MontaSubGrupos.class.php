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
* Montar os combos para seleção de grupo e subgrupo e também um campo
* texto para que fique de opcao para digitação do codigo do subgrupo
* de acordo com a mascara da configuracao.
* Data de Criação: 12/05/2003

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

include_once '../../../includes/Constante.inc.php';
include_once '../../../bibliotecas/mascaras.lib.php';
include_once ( CAM_REGRA."RGrupoItem.class.php"           );
include_once ( CAM_REGRA."RSubGrupoItem.class.php"        );
include_once ( CAM_REGRA."RConfiguracaoCompras.class.php" );
include_once ( CLA_CELULA );
include_once ( CLA_OBJETO );

/**
    * Classe que monta seleção de grupo e subgrupo

    * @package framework
    * @subpackage componentes
*/
class MontaSubGrupos extends Objeto
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
var $stActionPosterior;

/**
    * @access Private
    * @var String
*/
var $stActionAnterior;

/**
    * @access Private
    * @var String
*/
var $stTarget;

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
var $stMascara;

/**
    * @access Private
    * @var String
*/
var $stSelecionado;

/**
    * @access Private
    * @var String
*/
var $stValue;

/**
    * @access Private
    * @var String
*/
var $stAddFunction;

/**
    * @access Private
    * @var String
*/
var $inCodGrupo;

/**
    * @access Private
    * @var String
*/
var $inCodSubGrupo;

/**
    * @access Private
    * @var String
*/
var $boIFrame;

/**
    * @access Private
    * @var String
*/
var $boNull;

/**
    * @access Private
    * @var String
*/
var $boExecutaFrame;

/**
    * @access Private
    * @var String
*/
var $obRGrupoItem;

/**
    * @access Private
    * @var String
*/
var $obRSubGrupoItem;

/**
    * @access Private
    * @var String
*/
var $obRConfiguracaoCompras;

//SETTERS
/**
    * @access Public
    * @param String $Valor
*/
function setName($valor) { $this->stName           = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setRotulo($valor) { $this->stRotulo         = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setMascara($valor) { $this->stMascara        = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setSelecionado($valor) { $this->stSelecionado    = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setValue($valor) { $this->stValue          = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setAddFunction($valor) { $this->stAddFunction    = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setCodGrupo($valor) { $this->inCodGrupo       = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setCodSubGrupo($valor) { $this->inCodSubGrupo    = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setActionPosterior($valor) {  $this->stActionPosterior= $valor.'?'.Sessao::getId();}

/**
    * @access Public
    * @param String $Valor
*/
function setActionAnterior($valor) {  $this->stActionAnterior = $valor.'?'.Sessao::getId();}

/**
    * @access Public
    * @param String $Valor
*/
function setTarget($valor) { $this->stTarget         = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setTitle($valor) { $this->stTitle          = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setIFrame($valor) { $this->boIFrame         = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setNull($valor) { $this->boNull           = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setExecutaFrame($valor) { $this->boExecutaFrame   = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setRGrupoItem($valor) { $this->obRGrupoItem     = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setRSubGrupoItem($valor) { $this->obRSubGrupoItem  = $valor;                                }

/**
    * @access Public
    * @param String $Valor
*/
function setRConfiguracaoCompras($valor) { $this->obRConfiguracaoCompras = $valor;                         }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getName() { return $this->stName;                   }

/**
    * @access Public
    * @return String
*/
function getRotulo() { return $this->stRotulo;                 }

/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;                }

/**
    * @access Public
    * @return String
*/
function getSelecionado() { return $this->stSelecionado;            }

/**
    * @access Public
    * @return String
*/
function getValue() { return $this->stValue;                  }

/**
    * @access Public
    * @return String
*/
function getAddFunction() { return $this->stAddFunction;            }

/**
    * @access Public
    * @return String
*/
function getCodGrupo() { return $this->inCodGrupo;               }

/**
    * @access Public
    * @return String
*/
function getCodSubGrupo() { return $this->inCodSubGrupo;            }

/**
    * @access Public
    * @return String
*/
function getActionPosterior() { return $this->stActionPosterior;        }

/**
    * @access Public
    * @return String
*/
function getActionAnterior() { return $this->stActionAnterior;         }

/**
    * @access Public
    * @return String
*/
function getTarget() { return $this->stTarget;                 }

/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;                  }

/**
    * @access Public
    * @return String
*/
function getIFrame() { return $this->boIFrame;                 }

/**
    * @access Public
    * @return String
*/
function getNull() { return $this->boNull;                   }

/**
    * @access Public
    * @return String
*/
function getExecutaFrame() { return $this->boExecutaFrame;           }

/**
    * @access Public
    * @return String
*/
function getRGrupoItem() { return $this->obRGrupoItem;             }

/**
    * @access Public
    * @return String
*/
function getRSubGrupoItem() { return $this->obRSubGrupoItem;          }

/**
    * @access Public
    * @return String
*/
function getRConfiguracaoCompras() { return $this->obRConfiguracaoCompras;   }

/**
    * Método construtor
    * @access Public
*/
function MontaSubGrupos()
{
    $this->setRGrupoItem          ( new RGrupoItem );
    $this->setRSubGrupoItem       ( new RSubGrupoItem );
    $this->setRConfiguracaoCompras( new RConfiguracaoCompras );
    $this->setIFrame              ( false );
    $this->setNull                ( false );
    $this->setExecutaFrame        ( true );

    //Recupera mascara dos SubGrupos
    $this->obRConfiguracaoCompras->consultarConfiguracao( $rsConfiguracao );
    $stMascara = $this->obRConfiguracaoCompras->getMascSubGrupo();
    $this->setMascara( $stMascara );
}

function geraFormulario(&$obFormulario)
{
    //Monta combo de Grupos
    $this->obRGrupoItem->listar( $rsGrupoItem );
    $obCmbGrupoItem = new Select;
    $obCmbGrupoItem->setName      ( 'inCodGrupo'          );
    $obCmbGrupoItem->setValue     (  $this->getCodGrupo() );
    $obCmbGrupoItem->setStyle     ( "width: 200px"        );
    $obCmbGrupoItem->setCampoId   ( "cod_grupo"           );
    $obCmbGrupoItem->setCampoDesc ( "nom_grupo"           );
    $obCmbGrupoItem->addOption    ( "", "Selecione"       );
    $obCmbGrupoItem->obEvento->setOnChange("buscaValorComboComposto('buscaValoresSubGrupo' ,'".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."'); ".$this->getAddFunction());
    $obCmbGrupoItem->preencheCombo( $rsGrupoItem );

    //Monta combo de SubGrupos
    $obCmbSubGrupoItem = new Select;
    $obCmbSubGrupoItem->setName      ( 'inCodSubGrupo'         );
    $obCmbSubGrupoItem->setValue     ( $this->getCodSubGrupo() );
    $obCmbSubGrupoItem->setRotulo    ( $this->getRotulo()      );
    $obCmbSubGrupoItem->setStyle     ( "width: 200px"          );
    $obCmbSubGrupoItem->setCampoId   ( "cod_subgrupo"          );
    $obCmbSubGrupoItem->setCampoDesc ( "nom_subgrupo"          );
    $obCmbSubGrupoItem->addOption    ( "", "Selecione"         );
    $obCmbSubGrupoItem->obEvento->setOnChange("buscaValorComboComposto('buscaValoresSubGrupo' ,'".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."'); ".$this->getAddFunction());
    if ( $this->getNull() == false ) {
        $obCmbSubGrupoItem->setNull  ( false                   );
    }

    //Monta text com o valor da mascara do SubGrupo
    $obTxtMascSubGrupo = new TextBox;
    $obTxtMascSubGrupo->setName( "stMascSubGrupo" );
    $obTxtMascSubGrupo->setValue( $this->getValue() );
    $obTxtMascSubGrupo->setSize( 20 );
    $obTxtMascSubGrupo->setMaxLength( 20 );
    $obTxtMascSubGrupo->setNull( true );
    $obTxtMascSubGrupo->obEvento->setOnKeyUp("mascaraDinamico('".$this->getMascara()."', this, event);");
    $obTxtMascSubGrupo->obEvento->setOnChange("buscaValor('preencheSubGrupo', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', '".$this->getTarget()."', '".Sessao::getId()."');");
    if ( $this->getNull() == true ) {
        $stNomLabel = $this->getRotulo();
    } else {
        $stNomLabel = "*".$this->getRotulo();
    }
    $obFormulario->abreLinha();
    $obFormulario->addRotulo( "", $stNomLabel, 3 );
    $obFormulario->addCampo( $obTxtMascSubGrupo );
    $obFormulario->fechaLinha();

    $obFormulario->abreLinha();
    $obFormulario->addCampo( $obCmbGrupoItem );
    $obFormulario->fechaLinha();

    $obFormulario->abreLinha();
    $obFormulario->addCampo( $obCmbSubGrupoItem );
    $obFormulario->fechaLinha();

    if ( $this->getCodGrupo() ) {
        $this->buscaValoresSubGrupo();
    }
}

function buscaValoresSubGrupo()
{
    if ($_GET['stSelecionado'] == "inCodGrupo") {
        $_POST["inCodSubGrupo"] = "";
    }
    if ($_POST['inCodGrupo'] != "") {
        $this->obRSubGrupoItem->setCodGrupo( $_POST['inCodGrupo'] );
        $this->obRSubGrupoItem->listar( $rsSubGrupo, " ORDER BY nom_subgrupo ");
        if ( $rsSubGrupo->getNumLinhas() > -1 ) {
            $inContador = 1;
            $js .= "limpaSelect(f.inCodSubGrupo,0); \n";
            $js .= "f.inCodSubGrupo.options[0] = new Option('Selecione','', 'selected');\n";
            while ( !$rsSubGrupo->eof() ) {
                $inCodSubGrupo = $rsSubGrupo->getCampo("cod_subgrupo");
                $stNomSubGrupo = $rsSubGrupo->getCampo("nom_subgrupo");
                $selected      = "";
                if ($inCodSubGrupo == $_POST["inCodSubGrupo"]) {
                    $selected = "selected";
                }
                $js .= "f.inCodSubGrupo.options[$inContador] = new Option('".$stNomSubGrupo."','".$inCodSubGrupo."','".$selected."'); \n";
                $inContador++;
                $rsSubGrupo->proximo();
            }
        } else {
            $js .= "limpaSelect(f.inCodSubGrupo,0); \n";
            $js .= "f.inCodSubGrupo.options[0] = new Option('Selecione','', 'selected');\n";
        }

    } else {
        $js .= "limpaSelect(f.inCodSubGrupo,0); \n";
        $js .= "f.inCodSubGrupo.options[0] = new Option('Selecione','', 'selected');\n";
    }

    //monta mascara(parcial) com os valores JA SELECIONADOS
    if ($_GET['stSelecionado'] == "inCodGrupo") {
        $inCodSubGrupo = $_POST["inCodGrupo"];
    } else {
        $inCodSubGrupo = $_POST["inCodGrupo"].".".$_POST["inCodSubGrupo"];
    }

    $arMascTxt = validaMascaraDinamica( $this->getMascara(), $inCodSubGrupo );
    $js .= "f.stMascSubGrupo.value = '".$arMascTxt[1]."'; \n";

    if ( $this->getIFrame() == false ) {
        executaFrameOculto($js);
    } else {
        executaiFrameOculto($js);
    }
}

function preencheSubGrupo()
{
    $this->setMascara( $_POST['stMascSubGrupo'] );
    $arSubGrupo = preg_split( "/[^a-zA-Z0-9]/", $this->getMascara() );

    //preenche combo do Grupo
    $this->obRGrupoItem->listar( $rsGrupo, " ORDER BY nom_grupo" );
    if ( $rsGrupo->getNumLinhas() > -1 ) {
        $inContador = 1;
        $js .= "limpaSelect(f.inCodGrupo,0); \n";
        $js .= "f.inCodGrupo.options[0] = new Option('Selecione','', 'selected');\n";
        while ( !$rsGrupo->eof() ) {
            $inCodGrupo = $rsGrupo->getCampo("cod_grupo");
            $stNomGrupo = $rsGrupo->getCampo("nom_grupo");
            $selected   = "";
            if ($inCodGrupo == $arSubGrupo[0]) {
                $selected = "selected";
            }
            $js .= "f.inCodGrupo.options[$inContador] = new Option('".$stNomGrupo."','".$inCodGrupo."','".$selected."'); \n";
            $inContador++;
            $rsGrupo->proximo();
        }

        //preenche combo do SubGrupo
        $this->obRSubGrupoItem->setCodGrupo( $arSubGrupo[0] );
        $this->obRSubGrupoItem->listar( $rsSubGrupo, " ORDER BY nom_subgrupo");
        if ( $rsSubGrupo->getNumLinhas() > -1 ) {
            $inContador = 1;
            $js .= "limpaSelect(f.inCodSubGrupo,0); \n";
            $js .= "f.inCodSubGrupo.options[0] = new Option('Selecione','', 'selected');\n";
            while ( !$rsSubGrupo->eof() ) {
                $inCodSubGrupo = $rsSubGrupo->getCampo("cod_subgrupo");
                $stNomSubGrupo = $rsSubGrupo->getCampo("nom_subgrupo");
                $selected   = "";
                if ($inCodSubGrupo == $arSubGrupo[1]) {
                    $selected = "selected";
                }
                $js .= "f.inCodSubGrupo.options[$inContador] = new Option('".$stNomSubGrupo."','".$inCodSubGrupo."','".$selected."'); \n";
                $inContador++;
                $rsSubGrupo->proximo();
            }
        } else {
            $js .= "limpaSelect(f.inCodSubGrupo,0); \n";
            $js .= "f.inCodSubGrupo.options[0] = new Option('Selecione','', 'selected');\n";
        }

    } else {
        $js .= "limpaSelect(f.inCodGrupo,0); \n";
        $js .= "f.inCodGrupo.options[0] = new Option('Selecione','', 'selected');\n";
    }

    $js .= $this->getAddFunction();

    if ( $this->getExecutaFrame() == true ) {
        if ( $this->getIFrame() == false ) {
            executaFrameOculto($js);
        } else {
            executaiFrameOculto($js);
        }
    } else {
        return $js;
    }
}

}
?>
