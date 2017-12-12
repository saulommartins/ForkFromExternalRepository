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
* Classe de regra de interface para Dotação orçamentária
* Data de Criação: 26/07/2003

* @author Desenvolvedor: Marcelo Boezzio Paulino

* @package framework
* @subpackage componentes

Casos de uso: uc-02.01.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"               );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

/**
    * Classe de que monta o HTML da Dotacao Orcamentária

    * @package framework
    * @subpackage componentes
*/
class MontaDotacaoOrcamentaria extends Objeto
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
var $inCodOrgao;

/**
    * @access Private
    * @var String
*/
var $inCodUnidade;

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
var $obRDespesa;

/**
    * @access Private
    * @var String
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
function setRotulo($valor) { $this->stRotulo         = $valor;                                }

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
    * @param String $valor
*/
function setCodOrgao($valor) { $this->inCodOrgao       = $valor;                                }

/**
    * @access Public
    * @param String $valor
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
    * @param String $valor
*/
function setIFrame($valor) { $this->boIFrame         = $valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setNull($valor) { $this->boNull           = $valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setExecutaFrame($valor) { $this->boExecutaFrame   = $valor;                                }

/**
    * @access Public
    * @param String $valor
*/
function setRDespesa($valor) { $this->obRDespesa       = $valor;                                }

/**
    * @access Public
    * @param String $valor
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
    * @return String
*/
function getCodOrgao() { return $this->inCodOrgao;               }

/**
    * @access Public
    * @return String
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
function getRDespesa() { return $this->obRDespesa;               }

/**
    * @access Public
    * @return String
*/
function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento; }

/**
    * Método Construtor
    * @access Public
*/
function MontaDotacaoOrcamentaria()
{
    $this->setRDespesa              ( new ROrcamentoDespesa         );
    $this->setRConfiguracaoOrcamento( new ROrcamentoConfiguracao    );
    $this->setIFrame                ( false                         );
    $this->setNull                  ( true                         );
    $this->setExecutaFrame          ( true                          );

    $this->setMascara( $this->obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('masc_despesa') );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obFormulario
*/
function geraFormulario( &$obFormulario,$stAcao='',$nuCountDespesaExercicio='',$arDespesa=array())
{
        //Monta text com o valor da mascara do SubGrupo
        $obTxtMascDotacaoOrcamentaria = new TextBox;
        $obTxtMascDotacaoOrcamentaria->setName     ( "stDotacaoOrcamentaria"          );
        $obTxtMascDotacaoOrcamentaria->setValue    ( $this->getValue()                );
        $obTxtMascDotacaoOrcamentaria->setRotulo   ('Dotação Orçamentária'            );
        $obTxtMascDotacaoOrcamentaria->setTitle    ('Informe a dotação orçamentária.' );
        $obTxtMascDotacaoOrcamentaria->setSize     ( strlen($this->getMascara())      );
        $obTxtMascDotacaoOrcamentaria->setMaxLength( strlen($this->getMascara())      );
        $obTxtMascDotacaoOrcamentaria->setNull     ( $this->boNull                    );

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
        $obCmbOrgao->setTitle ( "Selecione o orgão orçamentário." );
        $obCmbOrgao->setNull      ( $this->boNull         );
        $obCmbOrgao->setCampoId   ( "[cod_orgao]-[num_orgao]-[exercicio]"   );
        $obCmbOrgao->setCampoDesc ( "[num_orgao] - [nom_orgao]" );
        $obCmbOrgao->addOption    ( "", "Selecione"       );
        $obCmbOrgao->obEvento->setOnChange("buscaValorComboComposto('buscaOrgaoUnidade', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."');" );
        $obCmbOrgao->preencheCombo( $rsOrgao );

        //Monta combo para seleção de UNIDADE ORCAMENTARIA
        $obCmbUnidade = new Select;
        $obCmbUnidade->setTitle ( "Selecione a unidade orçamentária." );
        $obCmbUnidade->setName      ( 'inCodUnidade'          );
        $obCmbUnidade->setValue     ( ''                      );
        $obCmbUnidade->setRotulo    ( 'Unidade'               );
        $obCmbUnidade->setStyle     ( "width: 400px"          );
        $obCmbUnidade->setCampoId   ( "num_unidade"           );
        $obCmbUnidade->setCampoDesc ( "[num_orgao].[num_unidade] - [nom_nom_unidade]" );
        $obCmbUnidade->addOption    ( "", "Selecione"         );
        $obCmbUnidade->obEvento->setOnChange("buscaValorComboComposto('buscaOrgaoUnidade', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."');" );
        $obCmbUnidade->setNull      ( $this->boNull           );

        //Monta combo para seleção de FUNÇÃO
        $this->obRDespesa->obROrcamentoFuncao->listar( $rsFuncao, "" );
        $obCmbFuncao = new Select;
        $obCmbFuncao->setTitle ( "Selecione a função." );
        $obCmbFuncao->setName      ( 'inCodFuncao'          );
        $obCmbFuncao->setValue     ( ''                     );
        $obCmbFuncao->setRotulo    ( 'Função'               );
        $obCmbFuncao->setStyle     ( "width: 400px"         );
        $obCmbFuncao->setNull      ( $this->boNull          );
        $obCmbFuncao->setCampoId   ( "cod_funcao"           );
        $obCmbFuncao->setCampoDesc ( "[cod_funcao] - [descricao]" );
        $obCmbFuncao->addOption    ( "", "Selecione"        );
        $obCmbFuncao->obEvento->setOnChange("buscaValorComboComposto('preencheMascara', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."');" );
        $obCmbFuncao->preencheCombo( $rsFuncao );

        //Monta combo para seleção de SUB-FUNÇÃO
        $this->obRDespesa->obROrcamentoSubfuncao->listar( $rsSubFuncao, "" );
        $obCmbSubFuncao = new Select;
        $obCmbSubFuncao->setTitle ( "Selecione a subfunção." );
        $obCmbSubFuncao->setName      ( 'inCodSubFuncao'       );
        $obCmbSubFuncao->setValue     ( ''                     );
        $obCmbSubFuncao->setRotulo    ( 'Subfunção'           );
        $obCmbSubFuncao->setStyle     ( "width: 400px"         );
        $obCmbSubFuncao->setNull      ( $this->boNull          );
        $obCmbSubFuncao->setCampoId   ( "cod_subfuncao"        );
        $obCmbSubFuncao->setCampoDesc ( "[cod_subfuncao] - [descricao]" );
        $obCmbSubFuncao->addOption    ( "", "Selecione"        );
        $obCmbSubFuncao->obEvento->setOnChange("buscaValorComboComposto('preencheMascara', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."');" );
        $obCmbSubFuncao->preencheCombo( $rsSubFuncao );

        //Monta combo para seleção de PROGRAMA
        $this->obRDespesa->obROrcamentoPrograma->listar( $rsPrograma, "ORDER BY num_programa" );
        $obCmbPrograma = new Select;
        $obCmbPrograma->setTitle ( "Selecione o programa." );
        $obCmbPrograma->setName      ( 'inCodPrograma'        );
        $obCmbPrograma->setValue     ( ''                     );
        $obCmbPrograma->setRotulo    ( 'Programa'             );
        $obCmbPrograma->setStyle     ( "width: 400px"         );
        $obCmbPrograma->setNull      ( $this->boNull          );
        $obCmbPrograma->setCampoId   ( "num_programa"         );
        $obCmbPrograma->setCampoDesc ( "[num_programa] - [descricao]" );
        $obCmbPrograma->addOption    ( "", "Selecione"        );
        $obCmbPrograma->obEvento->setOnChange("buscaValorComboComposto('preencheMascara', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."');" );
        $obCmbPrograma->preencheCombo( $rsPrograma );

        //Monta combo para seleção de PROJETO, ATIVIDADE OU OPERAÇÕES ('sw_pao')
        $this->obRDespesa->obROrcamentoProjetoAtividade->listar( $rsPAO, "ORDER BY num_acao" );
        $obCmbPAO = new Select;
        $obCmbPAO->setTitle ( "Selecione o PAO." );
        $obCmbPAO->setName      ( 'inCodPAO'                        );
        $obCmbPAO->setValue     ( ''            );
        $obCmbPAO->setRotulo    ( 'Projeto, Atividade ou Operações' );
        $obCmbPAO->setStyle     ( "width: 400px"                    );
        $obCmbPAO->setNull      ( $this->boNull                     );
        $obCmbPAO->setCampoId   ( "num_acao"                         );
        $obCmbPAO->setCampoDesc ( "[num_acao] - [nom_pao]"            );
        $obCmbPAO->addOption    ( "", "Selecione"                   );
        $obCmbPAO->obEvento->setOnChange("buscaValorComboComposto('preencheMascara', '".$this->getActionAnterior()."', '".$this->getActionPosterior()."', this.name, '".$this->getTarget()."');" );
        $obCmbPAO->preencheCombo( $rsPAO );

        // A partir daqui monta labels e hiddens

        // Monta Label com o valor da mascara do SubGrupo
        $obLblDotacaoOrcamentaria = new Label;
        $obLblDotacaoOrcamentaria->setRotulo ( 'Dotação Orçamentária' );
        $obLblDotacaoOrcamentaria->setValue  ( $this->getValue() );

        $obHdnDotacaoOrcamentaria = new Hidden;
        $obHdnDotacaoOrcamentaria->setName( 'stDotacaoOrcamentaria');
        $obHdnDotacaoOrcamentaria->setValue ( $this->getValue() );

        // Monta Label com ORGÃO
        $obLblOrgao = new Label;
        $obLblOrgao->setRotulo( 'Orgão' );

        $obHdnOrgao = new Hidden;
        $obHdnOrgao->setName( 'inCodOrgao' );

        while (!$rsOrgao->eof()) {
            if ($rsOrgao->getCampo('num_orgao') == $arDespesa[0]['num_orgao']) {
                $obLblOrgao->setValue ( $rsOrgao->getCampo('num_orgao').' - '.$rsOrgao->getCampo('nom_orgao') );
                $obHdnOrgao->setValue ( $rsOrgao->getCampo('cod_orgao').'-'.$rsOrgao->getCampo('num_orgao').'-'.$rsOrgao->getCampo('exercicio') );
                $rsOrgao->setUltimoElemento();
            }
            $rsOrgao->proximo();
        }

        // Monta Label com UNIDADE
        $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arDespesa[0]['num_orgao'] );
        $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $arDespeesa[0]['num_unidade'] );
        $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->listar( $rsUnidade, " ORDER BY num_unidade");

        $obLblUnidade = new Label;
        $obLblUnidade->setRotulo( 'Unidade' );

        $obHdnUnidade = new Hidden;
        $obHdnUnidade->setName ('inCodUnidade' );

        while (!$rsUnidade->eof()) {
            if ($rsUnidade->getCampo('num_unidade') == $arDespesa[0]['num_unidade']) {
                $obLblUnidade->setValue ( $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade') );
                $obHdnUnidade->setValue ( $rsUnidade->getCampo('num_unidade').'-'.$rsUnidade->getCampo('exercicio_unidade') );
            }
            $rsUnidade->proximo();
        }

        // Monta Label com FUNÇÂO
        $obLblFuncao = new Label;
        $obLblFuncao->setRotulo( 'Função' );

        $obHdnFuncao = new Hidden;
        $obHdnFuncao->setName ('inCodFuncao');

        while (!$rsFuncao->eof()) {
            if ($rsFuncao->getCampo('cod_funcao') == $arDespesa[0]['cod_funcao']) {
                $obLblFuncao->setValue ( $rsFuncao->getCampo('cod_funcao').' - '.$rsFuncao->getCampo('descricao') );
                $obHdnFuncao->setValue ( $rsFuncao->getCampo('cod_funcao') );
                $rsFuncao->setUltimoElemento();
            }
            $rsFuncao->proximo();
        }

        // Monta Label com SUBFUNÇÂO
        $obLblSubFuncao = new Label;
        $obLblSubFuncao->setRotulo( 'Subfunção' );

        $obHdnSubFuncao = new Hidden;
        $obHdnSubFuncao->setName ( 'inCodSubFuncao' );

        while (!$rsSubFuncao->eof()) {
            if ($rsSubFuncao->getCampo('cod_subfuncao') == $arDespesa[0]['cod_subfuncao']) {
                $obLblSubFuncao->setValue ( $rsSubFuncao->getCampo('cod_subfuncao').' - '.$rsSubFuncao->getCampo('descricao') );
                $obHdnSubFuncao->setValue ( $rsSubFuncao->getCampo('cod_subfuncao') );
                $rsSubFuncao->setUltimoElemento();
            }
            $rsSubFuncao->proximo();
        }

        // Monta Label com PROGRAMA
        $obLblPrograma = new Label;
        $obLblPrograma->setRotulo( 'Programa' );

        $obHdnPrograma = new Hidden;
        $obHdnPrograma->setName ( 'inCodPrograma' );

        while (!$rsPrograma->eof()) {
            if ($rsPrograma->getCampo('num_programa') == $arDespesa[0]['num_programa']) {
                $obLblPrograma->setValue ( $rsPrograma->getCampo('num_programa').' - '.$rsPrograma->getCampo('descricao') );
                $obHdnPrograma->setValue ( $rsPrograma->getCampo('cod_programa') );
                $rsPrograma->setUltimoElemento();
            }
            $rsPrograma->proximo();
        }

        //Monta Label com PROJETO, ATIVIDADE OU OPERAÇÕES
        $obLblPAO = new Label;
        $obLblPAO->setRotulo( 'Projeto, Atividade ou Operações' );

        $obHdnPAO = new Hidden;
        $obHdnPAO->setName ('inCodPAO');

        while (!$rsPAO->eof()) {
            if ($rsPAO->getCampo('num_acao') == $arDespesa[0]['num_acao']) {
                $obLblPAO->setValue ( $rsPAO->getCampo('num_acao').' - '.$rsPAO->getCampo('nom_pao') );
                $obHdnPAO->setValue ( $rsPAO->getCampo('num_pao') );
                $rsPAO->setUltimoElemento();
            }
            $rsPAO->proximo();
        }

        if ($stAcao == 'alterar' && $nuCountDespesaExercicio != 0) {
        // caso seja para alterar e o num_pao esteja em branco, entao pega os dados da acao do ppa para mostrar
        // se nao o PAO nao sera demonstrado
        if ($arDespesa[0]['num_pao'] == '') {
            $obLblPAO->setValue($arDespesa[0]['num_acao'].' - '.$arDespesa[0]['titulo']);
            $obHdnPAO->setValue($arDespesa[0]['num_acao']);
        } else {
            $this->obRDespesa->obROrcamentoProjetoAtividade->setExercicio($arDespesa[0]['exercicio']);
            $this->obRDespesa->obROrcamentoProjetoAtividade->setNumeroProjeto($arDespesa[0]['num_pao']);
            $this->obRDespesa->obROrcamentoProjetoAtividade->consultar( $rsPAO, "ORDER BY num_acao" );
            
            $obLblPAO->setValue($arDespesa[0]['num_acao'].' - '.$rsPAO->getCampo('nom_pao'));
            $obHdnPAO->setValue($arDespesa[0]['num_pao']);
        }
        
        $obFormulario->addComponente( $obLblDotacaoOrcamentaria );
        $obFormulario->addComponente( $obLblOrgao 				);
        $obFormulario->addComponente( $obLblUnidade 			);
        $obFormulario->addComponente( $obLblFuncao 				);
        $obFormulario->addComponente( $obLblSubFuncao 			);
        $obFormulario->addComponente( $obLblPrograma 			);
        $obFormulario->addComponente( $obLblPAO					);
        $obFormulario->addHidden    ( $obHdnDotacaoOrcamentaria );
        $obFormulario->addHidden    ( $obHdnOrgao               );
        $obFormulario->addHidden    ( $obHdnUnidade             );
        $obFormulario->addHidden    ( $obHdnFuncao              );
        $obFormulario->addHidden    ( $obHdnSubFuncao           );
        $obFormulario->addHidden    ( $obHdnPrograma            );
        $obFormulario->addHidden    ( $obHdnPAO                 );
   } else {
        $obFormulario->addComponente ( $obTxtMascDotacaoOrcamentaria );
        $obFormulario->addComponente ( $obCmbOrgao );
        $obFormulario->addComponente( $obCmbUnidade );
        $obFormulario->addComponente ( $obCmbFuncao );
        $obFormulario->addComponente ( $obCmbSubFuncao );
        $obFormulario->addComponente ( $obCmbPrograma );
        $obFormulario->addComponente ( $obCmbPAO );
    }

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
    if ($_GET['stSelecionado'] == "inCodOrgao") {
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
                $inCodUnidade   = $rsUnidade->getCampo("num_unidade")."-".$rsUnidade->getCampo("exercicio");
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
    if ($_GET['stSelecionado'] == "inCodOrgao") {
        $stDotacaoOrcamentaria = $arCodOrgao[1];
    } else {
        $stDotacaoOrcamentaria = $arCodOrgao[1].".".$arCodUnidade[0];
    }

    if ($_POST['stDotacaoOrcamentaria']) {
        $arDotacaoOrcamentaria = preg_split( "/[^a-zA-Z0-9]/", $_POST['stDotacaoOrcamentaria'] );
        $arDotacaoOrcamentaria[0] = $arCodOrgao[1];
        $arDotacaoOrcamentaria[1] = $arCodUnidade[0];
        $stDotacaoOrcamentaria = "";
        for ( $iCount = 2; $iCount <= count($arDotacaoOrcamentaria); $iCount++ ) {
            $stDotacaoOrcamentaria .= $arDotacaoOrcamentaria[$iCount].".";
        }
        $stDotacaoOrcamentaria = $arDotacaoOrcamentaria[0].".".$arDotacaoOrcamentaria[1].".".$stDotacaoOrcamentaria;
        $stDotacaoOrcamentaria = substr( $stDotacaoOrcamentaria, 0, strlen($stDotacaoOrcamentaria) - 1 );
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
    $js = '';
    $stRubricaDesmascarada = '';
    $arOrgaoUnidade = preg_split( "/[^a-zA-Z0-9]/", $_POST['stDotacaoOrcamentaria'] );
    foreach ($arOrgaoUnidade as $key => $valor) {
        if ($key == '6') {
            $stMascaraRubrica = $this->obRDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
            $stMascaraRubricaSemPontos = str_replace( '.' , '' , $stMascaraRubrica );
            $valor = str_pad( $valor , strlen($stMascaraRubricaSemPontos), 0 , STR_PAD_RIGHT );
            $arOrgaoUnidade[6] = $valor;
        }
    }
    for ( $iCount = 0; $iCount <= count($arOrgaoUnidade); $iCount++ ) {
        $stRubricaDesmascarada .= $arOrgaoUnidade[$iCount].".";
    }
    $stRubricaDesmascarada = substr( $stRubricaDesmascarada, 0, strlen($stRubricaDesmascarada) - 1 );

    $arMascDotacao = Mascara::validaMascaraDinamica( $this->getMascara() , $stRubricaDesmascarada );
    $js .= "f.stDotacaoOrcamentaria.value = '".$arMascDotacao[1]."'; \n";

    //preenche combo do Grupo
    $this->obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao, "ORDER BY exercicio, num_orgao" );
    if ( $rsOrgao->getNumLinhas() > -1 ) {
        $inContador = 1;
        $js .= "limpaSelect(f.inCodOrgao,0); \n";
        $js .= "f.inCodOrgao.options[0] = new Option('Selecione','', 'selected');\n";
        while ( !$rsOrgao->eof() ) {
            $inCodOrgao  = $rsOrgao->getCampo("cod_orgao");
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

    //prenenche combos de FUNCAO, SUBFUNCAO, PROGRAMA E PAO
    $arMascDotacao = preg_split( "/[^a-zA-Z0-9]/", $arMascDotacao[1] );
    foreach ($arMascDotacao as $key => $valor) {
        switch ($key) {
            case '2':
                $js .= "optionFuncao1 = recuperaOption(f.inCodFuncao,'".$valor."');";
                $js .= "f.inCodFuncao.options[optionFuncao1].selected = true;";
            break;
            case '3':
                $js .= "optionFuncao2 = recuperaOption(f.inCodSubFuncao,'".$valor."');";
                $js .= "f.inCodSubFuncao.options[optionFuncao2].selected = true;";
            break;
            case '4':
                $js .= "optionFuncao3 = recuperaOption(f.inCodPrograma,'".$valor."');";
                $js .= "f.inCodPrograma.options[optionFuncao3].selected = true;";
            break;
            case '5':
                $js .= "optionFuncao4 = recuperaOption(f.inCodPAO,'".$valor."');";
                $js .= "f.inCodPAO.options[optionFuncao4].selected = true;";
            break;
            case '6':
                $obMascara = new Mascara;

                $stMascaraRubricaSemPontos = str_replace( '.' , '' , $stMascaraRubrica );
                $valor = str_pad( $valor , strlen($stMascaraRubricaSemPontos), 0 , STR_PAD_RIGHT );

                $obMascara->setMascara( $stMascaraRubrica );
                $obMascara->mascaraDado( $valor );

                // Se o valor ficar vazio, não há a necessidade de fazer o processamento da pesquisa, pois logo após será feita uma verificação
                // desse valor para poder escrever a descrição da despesa. Com isso ganha-se em desempenho.
                if (trim($valor) != "") {
                    $this->obRDespesa->obROrcamentoClassificacaoDespesa->setMascara              ( $stMascaraRubrica );
                    $this->obRDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao    ( $valor            );
                    $this->obRDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao      );

                    if ($stDescricao != '') {
                        $js .= 'f.inCodDespesa.value = "'.$valor.'";';
                        $js .= 'if (d.getElementById("inCodDespesa").value != "") { d.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'"; }';
                    } else {
                        $null = "&nbsp;";
                        $js .= 'f.inCodDespesa.value = "";';
                        $js .= 'f.inCodDespesa.focus();';
                        $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
                        $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
                    }
                }
            break;
        }
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

    switch ($_GET['stSelecionado']) {
        case 'inCodFuncao':
            $arMascDotacao[2] = $_POST['inCodFuncao'];
        break;
        case 'inCodSubFuncao':
            $arMascDotacao[3] = $_POST['inCodSubFuncao'];
        break;
        case 'inCodPrograma':
            $arMascDotacao[4] = $_POST['inCodPrograma'];
        break;
        case 'inCodPAO':
            $arMascDotacao[5] = $_POST['inCodPAO'];
        break;
    }

    foreach ($arMascDotacao as $key => $valor) {
        $stMascDotacao .= $valor.".";
    }
    $stMascDotacao = substr( $stMascDotacao, 0, strlen($stMascDotacao) - 1 );

    $arMascDotacao = Mascara::validaMascaraDinamica( $this->getMascara(), $stMascDotacao );
    $js .= "f.stDotacaoOrcamentaria.value = '".$arMascDotacao[1]."'; \n";

    SistemaLegado::executaFrameOculto( $js );
}

}
?>
