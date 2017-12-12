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
* Classe de regra de interface para Orgao Unidade
* Data de Criação: 26/07/2003

* @author Desenvolvedor: Marcelo Boezzio Paulino

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"               );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

/**
    * Classe  de geração de componetes para cadastro de Orgao Unidade
*/
class MontaOrgaoUnidade extends Objeto
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
    * @var Integer
*/
var $inCodOrgao;

/**
    * @access Private
    * @var Integer
*/
var $inCodUnidade;

/**
    * @access Private
    * @var Boolean
*/
var $boIFrame;

/**
    * @access Private
    * @var Boolean
*/
var $boNull;

/**
    * @access Private
    * @var Boolean
*/
var $boExecutaFrame;

/**
    * @access Private
    * @var Object
*/
var $obRDespesa;

/**
    * @access Private
    * @var Object
*/
var $obRConfiguracaoOrcamento;

//SETTERS

/**
    * @access Public
    * @param String $valor
*/
function setName($valor) { $this->stName           = $valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setRotulo($valor) { $this->stRotulo         =
$valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara        = $valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setSelecionado($valor) { $this->stSelecionado    = $valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setValue($valor) { $this->stValue          = $valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setAddFunction($valor) { $this->stAddFunction    = $valor;                                }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodOrgao($valor) { $this->inCodOrgao       = $valor;                                }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodUnidade($valor) { $this->inCodUnidade    = $valor;                                 }

/**
    * @access Public
    * @param String $valor
*/
function setActionPosterior($valor) {  $this->stActionPosterior= $valor.'?'.Sessao::getId();}

/**
    * @access Public
    * @param String $valor
*/
function setActionAnterior($valor) {  $this->stActionAnterior = $valor.'?'.Sessao::getId();}

/**
    * @access Public
    * @param String $valor
*/
function setTarget($valor) { $this->stTarget         = $valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle          = $valor;                                }

/**
    * @access Public
    * @param Boolean $valor
*/
function setIFrame($valor) { $this->boIFrame         = $valor;                                }

/**
    * @access Public
    * @param Boolean $valor
*/
function setNull($valor) { $this->boNull           = $valor;                                }

/**
    * @access Public
    * @param Boolean $valor
*/
function setExecutaFrame($valor) { $this->boExecutaFrame   = $valor;                                }

/**
    * @access Public
    * @param Object $valor
*/
function setRDespesa($valor) { $this->obRDespesa       = $valor;                                }

/**
    * @access Public
    * @param Object $valor
*/
function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor;                        }

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
    * @return Integer
*/
function getCodOrgao() { return $this->inCodOrgao;               }

/**
    * @access Public
    * @return Integer
*/
function getCodUnidade() { return $this->inCodUnidade;             }

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
    * @return Boolean
*/
function getIFrame() { return $this->boIFrame;                 }

/**
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull;                   }

/**
    * @access Public
    * @return Boolean
*/
function getExecutaFrame() { return $this->boExecutaFrame;           }

/**
    * @access Public
    * @return Object
*/
function getRDespesa() { return $this->obRDespesa;               }

/**
    * @access Public
    * @return Object
*/
function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento; }

/**
    * Método construtor
    * @access Public
*/
function MontaOrgaoUnidade()
{
    $this->setRDespesa              ( new ROrcamentoDespesa      );
    $this->setRConfiguracaoOrcamento( new ROrcamentoConfiguracao );
    $this->setIFrame                ( false );
    $this->setNull                  ( false );
    $this->setExecutaFrame          ( true );

    $this->obRConfiguracaoOrcamento->consultarConfiguracao();
    $stMasc = $this->obRConfiguracaoOrcamento->getMascDespesa();
    $arMascDotacao = preg_split( "/[^a-zA-Z0-9]/", $stMasc );
    $stMascara = $arMascDotacao[0].".".$arMascDotacao[1];
    $this->setMascara( $stMascara );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obFormulario
*/
function geraFormulario(&$obFormulario)
{
    //Monta text com o valor da mascara do SubGrupo
    $obTxtMascDotacaoOrcamentaria = new TextBox;
    $obTxtMascDotacaoOrcamentaria->setName     ( "stDotacaoOrcamentaria" );
    $obTxtMascDotacaoOrcamentaria->setValue    ( $this->getValue() );
    $obTxtMascDotacaoOrcamentaria->setRotulo   ('Dotação Orçamentária');
    $obTxtMascDotacaoOrcamentaria->setSize     ( strlen($this->getMascara()) );
    $obTxtMascDotacaoOrcamentaria->setMaxLength( strlen($this->getMascara()) );
    $obTxtMascDotacaoOrcamentaria->setNull     ( true );
    $obTxtMascDotacaoOrcamentaria->obEvento->setOnFocus("selecionaValorCampo( this );");
    $obTxtMascDotacaoOrcamentaria->obEvento->setOnKeyUp("mascaraDinamico('".$this->getMascara()."', this, event);");
    $obTxtMascDotacaoOrcamentaria->obEvento->setOnChange("buscaValor('preencheUnidade', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', '".$this->getTarget()."', '".Sessao::getId()."');");

    //Monta combo para seleção de ORGÃO ORCAMENTARIO
    $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao, "ORDER BY num_orgao" );
    $obCmbOrgao = new Select;
    $obCmbOrgao->setName      ( 'inCodOrgao'          );
    $obCmbOrgao->setValue     (  $this->getCodOrgao() );
    $obCmbOrgao->setRotulo    ( 'Orgão'               );
    $obCmbOrgao->setStyle     ( "width: 400px"        );
    $obCmbOrgao->setNull      ( true                  );
    $obCmbOrgao->setCampoId   ( "[num_orgao]-[num_orgao]-[exercicio]"   );
    $obCmbOrgao->setCampoDesc ( "[num_orgao] - [nom_orgao]" );
    $obCmbOrgao->addOption    ( "", "Selecione"       );
    $obCmbOrgao->obEvento->setOnChange("buscaValorComboComposto('buscaOrgaoUnidade', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."');" );
    $obCmbOrgao->preencheCombo( $rsOrgao );

    //Monta combo para seleção de UNIDADE ORCAMENTARIA
    $obCmbUnidade = new Select;
    $obCmbUnidade->setName      ( 'inCodUnidade'          );
    $obCmbUnidade->setValue     ( $this->getCodUnidade()  );
    $obCmbUnidade->setRotulo    ( 'Unidade'               );
    $obCmbUnidade->setStyle     ( "width: 400px"          );
    $obCmbUnidade->setCampoId   ( "num_unidade"           );
    $obCmbUnidade->setCampoDesc ( "[num_orgao].[num_unidade] - [nom_nom_unidade]" );
    $obCmbUnidade->addOption    ( "", "Selecione"         );
    $obCmbUnidade->obEvento->setOnChange("buscaValorComboComposto('buscaOrgaoUnidade', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."');" );
    $obCmbUnidade->setNull      ( true                    );

    $obFormulario->abreLinha();
    $obFormulario->addRotulo( "Informe o orgão ou unidade.", "Órgão ou Unidade", 3 );
    $obFormulario->addCampo( $obTxtMascDotacaoOrcamentaria );
    $obFormulario->fechaLinha();

    $obFormulario->abreLinha();
    $obFormulario->addCampo( $obCmbOrgao );
    $obFormulario->fechaLinha();

    $obFormulario->abreLinha();
    $obFormulario->addCampo( $obCmbUnidade );
    $obFormulario->fechaLinha();

/*
    if ( $this->getCodOrgao() ) {
        $this->buscaValoresOrgaoUnidade();
    }*/
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function buscaValoresUnidade()
{
    $js = '';
    if ( isset($_GET['stSelecionado']) == "inCodOrgao" ) {
        $_POST["inCodUnidade"] = "";
    }
    if ($_POST['inCodOrgao'] != "") {
        $arCodOrgao = explode( '-' , $_POST['inCodOrgao'] );
        $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arCodOrgao[1] );
        $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->listar( $rsUnidade, " ORDER BY num_unidade");
        if ( $rsUnidade->getNumLinhas() > -1 ) {
            $inContador = 1;
            $js .= "limpaSelect(f.inCodUnidade,0); \n";
            $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
            while ( !$rsUnidade->eof() ) {
                $inCodUnidade   = $rsUnidade->getCampo("num_unidade")."-".$rsUnidade->getCampo("ano_exercicio");
                $stNomUnidade   = $rsUnidade->getCampo("num_unidade")." - ".$rsUnidade->getCampo("nom_unidade");
                $selected       = "";
                if ($inCodUnidade == $_POST["inCodUnidade"]) {
                    $selected = "selected";
                }
                $js .= "f.inCodUnidade.options[$inContador] = new Option('".$stNomUnidade."','".$inCodUnidade."','".$selected."'); \n";
                $inContador++;
                $rsUnidade->proximo();
            }
        } else {
            $js .= "limpaSelect(f.inCodUnidade,0); \n";
            $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
        }

    } else {
        $js .= "limpaSelect(f.inCodUnidade,0); \n";
        $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
    }

    //monta mascara(parcial) com os valores JA SELECIONADOS
    $arCodOrgao   = explode( "-" , $_POST["inCodOrgao"]   );
    $arCodUnidade = explode( "-" , $_POST["inCodUnidade"] );

    if ( isset($_POST['stDotacaoOrcamentaria']) ) {
        $arDotacaoOrcamentaria = preg_split( "/[^a-zA-Z0-9]/", $_POST['stDotacaoOrcamentaria'] );
        $arDotacaoOrcamentaria[0] = $arCodOrgao[1];
        $arDotacaoOrcamentaria[1] = $arCodUnidade[0];
        $stDotacaoOrcamentaria = "";
        for ( $iCount = 2; $iCount <= count($arDotacaoOrcamentaria); $iCount++ ) {
            $stDotacaoOrcamentaria .= $arDotacaoOrcamentaria[$iCount].".";
        }
        $stDotacaoOrcamentaria = $arDotacaoOrcamentaria[0].".".$arDotacaoOrcamentaria[1].".".$stDotacaoOrcamentaria;
        $stDotacaoOrcamentaria = substr( $stDotacaoOrcamentaria, 0, strlen($stDotacaoOrcamentaria) - 1 );
    } else {
        if ( isset($_GET['stSelecionado']) == "inCodOrgao" ) {
            $stDotacaoOrcamentaria = $arCodOrgao[1];
        } else {
            $stDotacaoOrcamentaria = $arCodOrgao[1].".".$arCodUnidade[0];
        }
    }

    $arMascDotacao = Mascara::validaMascaraDinamica( $this->getMascara(), $stDotacaoOrcamentaria );
    $js .= "f.stDotacaoOrcamentaria.value = '".$arMascDotacao[1]."'; \n";

    if ( $this->getIFrame() == false ) {
        SistemaLegado::executaFrameOculto($js);
    } else {
        SistemaLegado::executaiFrameOculto($js);
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @return String
*/
function preencheUnidade()
{
    $arOrgaoUnidade = preg_split( "/[^a-zA-Z0-9]/", $_POST['stDotacaoOrcamentaria'] );
    $stRubricaDesmascarada ='';
    foreach ($arOrgaoUnidade as $key => $valor) {
        if ($key == '6') {
            $stMascaraRubrica = $this->obRDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
            $stMascaraRubricaSemPontos = str_replace( '.' , '' , $stMascaraRubrica );
            $valor = str_pad( $valor , strlen($stMascaraRubricaSemPontos), 0 , STR_PAD_RIGHT );
            $arOrgaoUnidade[6] = $valor;
        }
    }
    for ( $iCount = 0; $iCount < count($arOrgaoUnidade); $iCount++ ) {
        $stRubricaDesmascarada .= $arOrgaoUnidade[$iCount].".";
    }
    $stRubricaDesmascarada = substr( $stRubricaDesmascarada, 0, strlen($stRubricaDesmascarada) - 1 );

    $arMascDotacao = Mascara::validaMascaraDinamica( $this->getMascara() , $stRubricaDesmascarada );

    if ( strlen( $_POST['stDotacaoOrcamentaria'] ) > 0 ) {
        $js = "f.stDotacaoOrcamentaria.value = '".$arMascDotacao[1]."'; \n";
    } else {
        $js = "f.stDotacaoOrcamentaria.value = ''; \n";
    }

    //preenche combo do Grupo
    $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao, "ORDER BY exercicio, num_orgao, nom_orgao" );
    if ( $rsOrgao->getNumLinhas() > -1 ) {
        $inContador = 1;
        $js .= "limpaSelect(f.inCodOrgao,0); \n";
        $js .= "f.inCodOrgao.options[0] = new Option('Selecione','', 'selected');\n";
        while ( !$rsOrgao->eof() ) {
            $inCodOrgao  = $rsOrgao->getCampo("num_orgao");
            $inNumOrgao  = $rsOrgao->getCampo("num_orgao");
            $stExercicio = $rsOrgao->getCampo("exercicio");
            $stNomOrgao  = $rsOrgao->getCampo("nom_orgao");
            $stCodOrgao  = $inCodOrgao."-".$inNumOrgao."-".$stExercicio;
            $stNomOrgao  = $inNumOrgao." - ".$stNomOrgao;
            $selected    = "";
            if ($inNumOrgao == $arOrgaoUnidade[0]) {
                $selected = "selected";
            }
            $js .= "f.inCodOrgao.options[$inContador] = new Option('".$stNomOrgao."','".$stCodOrgao."','".$selected."'); \n";
            $inContador++;
            $rsOrgao->proximo();
        }

        //preenche combo de Unidade
        $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arOrgaoUnidade[0] );
        $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->listar( $rsUnidade, " ORDER BY num_unidade");
        if ( $rsUnidade->getNumLinhas() > -1 ) {
            $inContador = 1;
            $js .= "limpaSelect(f.inCodUnidade,0); \n";
            $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
            while ( !$rsUnidade->eof() ) {
                $inCodUnidade  = $rsUnidade->getCampo("num_unidade")."-".$rsUnidade->getCampo("exercicio");
                $stNomUnidade  = $rsUnidade->getCampo("num_unidade")." - ".$rsUnidade->getCampo("nom_unidade");
                $selected      = "";
                if ( $rsUnidade->getCampo("num_unidade") == $arOrgaoUnidade[1] ) {
                    $selected = "selected";
                }
                $js .= "f.inCodUnidade.options[$inContador] = new Option('".$stNomUnidade."','".$inCodUnidade."','".$selected."'); \n";
                $inContador++;
                $rsUnidade->proximo();
            }
        } else {
            $js .= "limpaSelect(f.inCodUnidade,0); \n";
            $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
        }

    } else {
        $js .= "limpaSelect(f.inCodOrgao,0); \n";
        $js .= "f.inCodOrgao.options[0] = new Option('Selecione','', 'selected');\n";
    }

    $js .= $this->getAddFunction();

    if ( $this->getExecutaFrame() == true ) {
        if ( $this->getIFrame() == false ) {
            SistemaLegado::executaFrameOculto($js);
        } else {
            SistemaLegado::executaiFrameOculto($js);
        }
    } else {
        return $js;
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function preencheMascara()
{
    if (!$_POST['stDotacaoOrcamentaria']) {
        $arMascDotacao = preg_split( "/[^a-zA-Z0-9]/", $this->getMascara() );
        foreach ($arMascDotacao as $key => $valor) {
            $arMascDotacao[$key] = 0;
        }
    } else {
        $arMascDotacao = preg_split( "/[^a-zA-Z0-9]/", $_POST['stDotacaoOrcamentaria'] );
    }

    $stMascDotacao .= $arMascDotacao[0].".".$arMascDotacao[1];
    $stMascDotacao = substr( $stMascDotacao, 0, strlen($stMascDotacao) - 1 );

    $arMascDotacao = Mascara::validaMascaraDinamica( $this->getMascara(), $stMascDotacao );
    $js .= "f.stDotacaoOrcamentaria.value = '".$arMascDotacao[1]."'; \n";

    SistemaLegado::executaFrameOculto( $js );
}

}
?>
