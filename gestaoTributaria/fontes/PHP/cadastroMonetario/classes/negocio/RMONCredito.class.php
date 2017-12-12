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
    * Classe de regra de negócio para MONETARIO.CREDITO
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONCredito.class.php 63344 2015-08-19 18:51:30Z arthur $

* Casos de uso: uc-05.05.10
                uc-02.04.03
                uc-02.04.33
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_MAPEAMENTO."TMONEspecieCredito.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCreditoCarteira.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCreditoNorma.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONRegraDesoneracaoCredito.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCreditoMoeda.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCreditoIndicador.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCreditoContaCorrente.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONNaturezaCredito.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONGeneroCredito.class.php");
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php");

class RMONCredito
{
/**
    * @access Private
    * @var Integer
*/
var $inCodCarteira;

/**
    * @access Private
    * @var Integer
*/
var $inCodConvenio;

/**
    * @access Private
    * @var Integer
*/
var $inCodCredito;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var Integer
*/
var $inCodNatureza;
/**
    * @access Private
    * @var String
*/
var $stNomeNatureza;
/**
    * @access Private
    * @var Integer
*/
var $inCodEspecie;
/**
    * @access Private
    * @var String
*/
var $stNomeEspecie;
/**
    * @access Private
    * @var Integer
*/
var $inCodNorma;
/**
    * @access Private
    * @var String
*/
var $stNomeNorma;
/**
    * @access Private
    * @var Integer
*/
var $inCodMoeda;
/**
    * @access Private
    * @var String
*/
var $stNomeMoeda;
/**
    * @access Private
    * @var Integer
*/
var $inCodIndicador;
/**
    * @access Private
    * @var String
*/
var $stNomeIndicador;
/**
    * @access Private
    * @var Integer
*/
var $inCodGenero;
/**
    * @access Private
    * @var Boolean
*/
var $inOrdem;
/**
    * @access Private
    * @var Boolean
*/
var $boDesconto;
/**
    * @access Private
    * @var String
*/
var $stMascaraCredito;
/**
    * @access Private
    * @var String
*/
var $stNomeGenero;
/**
    * @access Private
    * @var String
*/
var $stNomeFuncao;
/**
    * @access Private
    * @var String
*/
var $stValorCorrespondente;
/**
    * @access Private
    * @var ARRAY
*/
var $CodigosAcrescimos;

/**
    * @access Private
    * @var Object
*/
var $obTMONCreditoCarteira;

/**
    * @access Private
    * @var Object
*/
var $obTMONCreditoContaCorrente;

/**
    * @access Private
    * @var Object
*/
var $obTMONCredito;
/**
    * @access Private
    * @var Object
*/
var $obRNormaCredito;
/**
    * @access Private
    * @var Object
*/
var $obRCreditoIndicador;
/**
    * @access Private
    * @var Object
*/
var $obRCreditoMoeda;
/**
    * @access Private
    * @var Object
*/
var $obTMONEspecieCredito;
/**
    * @access Private
    * @var Object
*/
var $obTMONNaturezaCredito;
/**
    * @access Private
    * @var Object
*/
var $obTMONGeneroCredito;
/**
    * @access Private
    * @var Object
*/
var $obRNorma;
/**
    * Referencia ao Grupo de Credito.
    * @access Private
    * @var Object
*/
var $roRARRGrupo;

/**
    * @access Private
    * @var Integer
*/
var $inCodBanco;

/**
    * @access Private
    * @var Integer
*/
var $inCodAgencia;

/**
    * @access Private
    * @var Integer
*/
var $inCodConta;
/**
    * @access Public
    * @var Array
*/
var $obRMONAcrescimo;
/**
    * @access Public
    * @var Array
*/
var $stCodEntidade;
/**
    * @access Public
    * @var Array
*/
var $stExercicio;
var $stCodFuncaoDesoneracao;
var $arCodNorma;
//SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCodFuncaoDesoneracao($valor) { $this->stCodFuncaoDesoneracao = $valor; }
function setCodConta($valor) { $this->inCodConta = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodAgencia($valor) { $this->inCodAgencia = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodBanco($valor) { $this->inCodBanco = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodCarteira($valor) { $this->inCodCarteira = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodConvenio($valor) { $this->inCodConvenio = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodCredito($valor) { $this->inCodCredito = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setDescricao($valor) { $this->stDescricao = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodEspecie($valor) { $this->inCodEspecie = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setNomeEspecie($valor) { $this->stNomeEspecie = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodGenero($valor) { $this->inCodGenero = $valor; }
/**
    * @access Public
    * @param Booelan $valor
*/
function setOrdem($valor) { $this->inOrdem = $valor; }
/**
    * @access Public
    * @param Booelan $valor
*/
function setDesconto($valor) { $this->boDesconto = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setNomeGenero($valor) { $this->stNomeGenero = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodNatureza($valor) { $this->inCodNatureza = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodEntidade($valor) { $this->stCodEntidade = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodNorma($valor) { $this->inCodNorma = $valor; }
function setArCodNorma($valor) { $this->arCodNorma = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNomeNorma($valor) { $this->stNomeNorma = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodMoeda($valor) { $this->inCodMoeda = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNomeMoeda($valor) { $this->stNomeMoeda = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodIndicador($valor) { $this->inCodIndicador = $valor; }
/**
    * @access Public
    * @param Stringr
*/
function setNomeIndicador($valor) { $this->stNomeIndicador = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNomeNatureza($valor) { $this->stNomeNatureza = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNomeFuncao($valor) { $this->stNomeFuncao = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setValorCorrespondente($valor) { $this->stValorCorrespondente= $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascaraCredito($valor) { $this->stMascaraCredito = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setAcrescimos($valor) { $this->CodigosAcrescimos = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getCodConta() { return $this->inCodConta; }

/**
    * @access Public
    * @return Integer
*/
function getCodAgencia() { return $this->inCodAgencia; }

/**
    * @access Public
    * @return Integer
*/
function getCodBanco() { return $this->inCodBanco; }

/**
    * @access Public
    * @return Float
*/
function getCodCarteira() { return $this->inCodCarteira; }

/**
    * @access Public
    * @return Float
*/
function getCodConvenio() { return $this->inCodConvenio; }

/**
    * @access Public
    * @return Float
*/
function getCodCredito() { return $this->inCodCredito; }
/**
    * @access Public
    * @return Float
*/
function getDescricao() { return $this->stDescricao; }
/**
    * @access Public
    * @return Float
*/
function getCodEspecie() { return $this->inCodEspecie; }
/**
    * @access Public
    * @return Float
*/
function getNomeEspecie() { return $this->stNomeEspecie; }
/**
    * @access Public
    * @return Float
*/
function getCodGenero() { return $this->inCodGenero; }
/**
    * @access Public
    * @return Boolean
*/
function getOrdem() { return $this->inOrdem; }
/**
    * @access Public
    * @return Boolean
*/
function getDesconto() { return $this->boDesconto; }
/**
    * @access Public
    * @return Float
*/
function getCodNorma() { return $this->inCodNorma; }
function getArCodNorma() { return $this->arCodNorma; }
/**
    * @access Public
    * @return String
*/
function getNomeNorma() { return $this->stNomeNorma; }
/**
    * @access Public
    * @return Float
*/
function getCodIndicador() { return $this->inCodIndicador; }
/**
    * @access Public
    * @return String
*/
function getNomeIndicador() { return $this->stNomeIndicador;  }
/**
    * @access Public
    * @return Float
*/
function getCodMoeda() { return $this->inCodMoeda; }
/**
    * @access Public
    * @return String
*/
function getNomeMoeda() { return $this->stNomeMoeda; }
/**
    * @access Public
    * @return String
*/
function getNomeGenero() { return $this->stNomeGenero; }
/**
    * @access Public
    * @return Array
*/
function getAcrescimos() { return $this->CodigosAcrescimos; }
/**
    * @access Public
    * @return Float
*/
function getCodNatureza() { return $this->inCodNatureza; }
/**
    * @access Public
    * @return Float
*/
function getCodEntidade() { return $this->stCodEntidade; }
/**
    * @access Public
    * @return Float
*/
function getExercicio() { return $this->stExercicio; }
/**
    * @access Public
    * @return Float
*/
function getNomeNatureza() { return $this->stNomeNatureza; }
/**
    * @access Public
    * @return String
*/
function getMascaraCredito() { return $this->stMascaraCredito; }
/**
    * @access Public
    * @return String
*/
function getNomeFuncao() { return $this->stNomeFuncao; }
/**
    * @access Public
    * @return String
*/
function getValorCorrespondente() { return $this->stValorCorrespondente; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RMONCredito()
{
    $this->obTransacao      = new Transacao;
    // instancia mapeamentos
    $this->obTMONCreditoCarteira = new TMONCreditoCarteira;
    $this->obTMONCredito    = new TMONCredito;
    $this->obTMONCreditoNorma    = new TMONCreditoNorma;
    $this->obTMONCreditoIndicador= new TMONCreditoIndicador;
    $this->obTMONCreditoMoeda    = new TMONCreditoMoeda;
    $this->obTMONEspecie    = new TMONEspecieCredito;
    $this->obTMONGenero     = new TMONGeneroCredito;
    $this->obTMONNatureza   = new TMONNaturezaCredito;
    $this->obTMONCreditoContaCorrente = new TMONCreditoContaCorrente;
    // instancia regras
    $this->obRNorma         = new RNorma;

    $this->obRMONAcrescimo = new RMONAcrescimo();
}

/**
    * BuscaCreditoCarteira
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function BuscaCreditoCarteira(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n cod_credito = '".$this->getCodCredito()."' AND ";
    }
    if ( $this->getCodEspecie() ) {
        $stFiltro .= " \n cod_especie = '".$this->getCodEspecie()."' AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " \n cod_genero = '".$this->getCodGenero()."' AND ";
    }
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n cod_natureza = '".$this->getCodNatureza()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = "     WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = "";
    $obErro = $this->obTMONCreditoCarteira->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Inclui os dados referentes a Credito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCredito($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = new Erro;
    $timestamp = date('Y-m-d h:m:s');

    if ( !$this->VerificaNomeCredito() ) {

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $obErro = $this->obTMONCredito->proximoCod( $this->inCodCredito, $boTransacao );
            if ( !$obErro->ocorreu() ) {

                $this->obTMONCredito->setDado( "cod_credito", $this->getCodCredito() );
                $this->obTMONCredito->setDado( "cod_natureza", $this->getCodNatureza() );
                $this->obTMONCredito->setDado( "cod_genero", $this->getCodGenero() );
                $this->obTMONCredito->setDado( "cod_especie", $this->getCodEspecie() );
                $this->obTMONCredito->setDado( "cod_convenio", $this->getCodConvenio() );
                $this->obTMONCredito->setDado( "descricao_credito", $this->getDescricao() );

                $obErro = $this->obTMONCredito->inclusao( $boTransacao );

                if ($this->stCodFuncaoDesoneracao) {
                    $arCodFuncao = explode( ".", $this->stCodFuncaoDesoneracao );
                    $obTMONRegraDesoneracaoCredito = new TMONRegraDesoneracaoCredito;
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_credito", $this->getCodCredito() );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_natureza", $this->getCodNatureza() );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_genero", $this->getCodGenero() );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_especie", $this->getCodEspecie() );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_modulo", $arCodFuncao[0] );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_biblioteca", $arCodFuncao[1] );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_funcao", $arCodFuncao[2] );
                    $obTMONRegraDesoneracaoCredito->inclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {
                    $this->obTMONCreditoContaCorrente->setDado( "cod_agencia", $this->inCodAgencia );
                    $this->obTMONCreditoContaCorrente->setDado( "cod_banco", $this->inCodBanco );
                    $this->obTMONCreditoContaCorrente->setDado( "cod_conta_corrente", $this->inCodConta );
                    $this->obTMONCreditoContaCorrente->setDado( "cod_convenio", $this->getCodConvenio() );

                    $this->obTMONCreditoContaCorrente->setDado( "cod_credito", $this->getCodCredito() );
                    $this->obTMONCreditoContaCorrente->setDado( "cod_natureza", $this->getCodNatureza() );
                    $this->obTMONCreditoContaCorrente->setDado( "cod_genero", $this->getCodGenero() );
                    $this->obTMONCreditoContaCorrente->setDado( "cod_especie", $this->getCodEspecie() );

                    $obErro = $this->obTMONCreditoContaCorrente->inclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() && $this->getCodCarteira() ) {
                    //incluindo dados na tabela monetario.credito_carteira
                    $this->obTMONCreditoCarteira->setDado( "cod_credito", $this->getCodCredito() );
                    $this->obTMONCreditoCarteira->setDado( "cod_natureza", $this->getCodNatureza() );
                    $this->obTMONCreditoCarteira->setDado( "cod_genero", $this->getCodGenero() );
                    $this->obTMONCreditoCarteira->setDado( "cod_especie", $this->getCodEspecie() );
                    $this->obTMONCreditoCarteira->setDado( "cod_carteira", $this->getCodCarteira() );
                    $this->obTMONCreditoCarteira->setDado( "cod_convenio", $this->getCodConvenio() );

                    $obErro = $this->obTMONCreditoCarteira->inclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {
                    $arNormas = $this->getArCodNorma();
                    $this->obTMONCreditoNorma->setDado( "cod_credito", $this->getCodCredito() );
                    $this->obTMONCreditoNorma->setDado( "cod_natureza", $this->getCodNatureza() );
                    $this->obTMONCreditoNorma->setDado( "cod_genero", $this->getCodGenero() );
                    $this->obTMONCreditoNorma->setDado( "cod_especie", $this->getCodEspecie() );
                    $obErro = $this->obTMONCreditoNorma->exclusao( $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        for ( $inX=0; $inX<count($arNormas); $inX++ ) {
                            //insercao na tabela CREDITO_NORMA
                            $this->obTMONCreditoNorma->setDado( "cod_norma", $arNormas[$inX]["inCodNorma"] );
                            $this->obTMONCreditoNorma->setDado( "dt_inicio_vigencia", $arNormas[$inX]["dtVigenciaInicio"] );
                            $obErro = $this->obTMONCreditoNorma->inclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                    }
                }

                if ( $this->getCodIndicador () ) {
                    if ( !$obErro->ocorreu() ) {
                        //insercao na tabela CREDITO_INDICADOR
                        $this->obTMONCreditoIndicador->setDado( "cod_credito", $this->getCodCredito() );
                        $this->obTMONCreditoIndicador->setDado( "cod_natureza", $this->getCodNatureza() );
                        $this->obTMONCreditoIndicador->setDado( "cod_genero", $this->getCodGenero() );
                        $this->obTMONCreditoIndicador->setDado( "cod_especie", $this->getCodEspecie() );
                        $this->obTMONCreditoIndicador->setDado( "cod_indicador", $this->getCodIndicador() );
                        $this->obTMONCreditoIndicador->setDado( "timestamp", $timestamp );
                        $obErro = $this->obTMONCreditoIndicador->inclusao( $boTransacao );
                    }
                }

                if ( $this->getCodMoeda () ) {
                    if ( !$obErro->ocorreu() ) {
                //insercao na tabela CREDITO_MOEDA
                        $this->obTMONCreditoMoeda->setDado( "cod_credito", $this->getCodCredito() );
                        $this->obTMONCreditoMoeda->setDado( "cod_natureza", $this->getCodNatureza() );
                        $this->obTMONCreditoMoeda->setDado( "cod_genero", $this->getCodGenero() );
                        $this->obTMONCreditoMoeda->setDado( "cod_especie", $this->getCodEspecie() );
                        $this->obTMONCreditoMoeda->setDado( "cod_moeda", $this->getCodMoeda () );
                        $this->obTMONCreditoMoeda->setDado( "timestamp", $timestamp );
                        $obErro = $this->obTMONCreditoMoeda->inclusao( $boTransacao );

                    }
                }

                //Inserindo Credito e CreditoAcrescimo
                if ( $this->getAcrescimos () ) {
                    if ( !$obErro->ocorreu() ) {
                        include_once ( CAM_GT_MON_NEGOCIO."RMONCreditoAcrescimo.class.php");
                        $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
                        $obRMONCreditoAcrescimo->setCodCredito ( $this->getCodCredito() );

                            //laco de insercao
                        $NRegistros = count (  $this->getAcrescimos()  );
                        $cont = 0;
                        $newArAcrescimos = array();
                        $newArAcrescimos = $this->getAcrescimos();

                        //echo 'Genero: '.$this->getCodGenero().'<br>';
                        $obRMONCreditoAcrescimo->setCodGenero   ( $this->getCodGenero() );

                        //echo 'Natureza: '.$this->getCodNatureza().'<br>';
                        $obRMONCreditoAcrescimo->setCodNatureza ( $this->getCodNatureza() );

                        //echo 'Especie: '.$this->getCodEspecie().'<br>';
                        $obRMONCreditoAcrescimo->setCodEspecie  ( $this->getCodEspecie() );

                        while ($cont < $NRegistros) {

                            $regCodAcrescimo = $newArAcrescimos[$cont]['cod_acrescimo'];
                            $regTipo = $newArAcrescimos[$cont]['cod_tipo'];
                            $obRMONCreditoAcrescimo->setCodAcrescimo ( $regCodAcrescimo );
                            $obRMONCreditoAcrescimo->setCodTipo ( $regTipo );
                            $obRMONCreditoAcrescimo->incluirCreditoAcrescimo();

                            $cont++;
                        }
                    }//se nao tem erro
                }//se tem acrescimo a inserir
            }//fim proximocod
        }//fim abre transacao
    } else { //fim verifica nome credito
        $obErro->setDescricao("Crédito já cadastrado no Sistema! (". $this->getDescricao () .")");
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONCredito );

    return $obErro;
}

/**
    * Exclui os dados referentes a Credito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirCredito($boTransacao = "")
{
    include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php");
    include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"    );
    include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"      );

    $obDesoneracao = new RARRDesoneracao();
    $obCalculo     = new RARRCalculo();
    $obGrupo       = new RARRGrupo();

    $rsCalculo     = new RecordSet();
    $rsGrupo       = new RecordSet();
    $rsMaskara     = new RecordSet();

    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $stFiltro = '';
    $stOrder  = '';

    $stFiltro = " WHERE calculo.cod_credito  = ".$_REQUEST['inCodCredito']."          \n";
    $stFiltro.= "   AND calculo.cod_natureza = ".$_REQUEST['inCodNatureza']."         \n";
    $stFiltro.= "   AND calculo.cod_genero   = ".$_REQUEST['inCodGenero']."           \n";
    $stFiltro.= "   AND calculo.cod_especie  = ".$_REQUEST['inCodEspecie']."          \n";

    $stOrder = "  ORDER BY calculo.exercicio DESC                                     \n";
    $stOrder.= "  LIMIT 1                                                             \n";

    $obCalculo->obTARRCalculo->recuperaTodos( $rsCalculo, $stFiltro, $stOrder, $boTransacao );

    $obGrupo->obRMONCredito->setCodCredito ( $_REQUEST['inCodCredito' ] );
    $obGrupo->obRMONCredito->setCodNatureza( $_REQUEST['inCodNatureza'] );
    $obGrupo->obRMONCredito->setCodGenero  ( $_REQUEST['inCodGenero'  ] );
    $obGrupo->obRMONCredito->setCodEspecie ( $_REQUEST['inCodEspecie' ] );

    $this->listarCreditosPopUp($rsMaskara,$boTransacao);
    $this->consultarMascaraCredito();

    $stMascaraCredito = $this->getMascaraCredito();
    $arMascaraCredito = explode(".", $stMascaraCredito);

    for ($inX=0; $inX<4; $inX++) {
        $arMascaraCredito[$inX] = strlen($arMascaraCredito[$inX]);
    }

    if ( !$rsMaskara->Eof() ) {
        $arDados = $rsMaskara->getElementos();
        for ( $inX=0; $inX<count($arDados); $inX++) {
            $arDados[$inX]["cod_credito" ] = sprintf( "%0".$arMascaraCredito[0]."d", $arDados[$inX]["cod_credito"]  );
            $arDados[$inX]["cod_especie" ] = sprintf( "%0".$arMascaraCredito[1]."d", $arDados[$inX]["cod_especie"]  );
            $arDados[$inX]["cod_genero"  ] = sprintf( "%0".$arMascaraCredito[2]."d", $arDados[$inX]["cod_genero"]   );
            $arDados[$inX]["cod_natureza"] = sprintf( "%0".$arMascaraCredito[3]."d", $arDados[$inX]["cod_natureza"] );
        }
        $rsMaskara->preenche( $arDados );
        $rsMaskara->setPrimeiroElemento();
    }

    $stMaskara = $arDados[0]['cod_credito' ].".";
    $stMaskara.= $arDados[0]['cod_especie' ].".";
    $stMaskara.= $arDados[0]['cod_genero'  ].".";
    $stMaskara.= $arDados[0]['cod_natureza'];

    $stMaskaraDesc = $arDados[0]['descricao_credito'];

    if ( $rsCalculo->getNumLinhas() < 1 ) {

        $obGrupo->listarCreditos($rsGrupo);

        if ( $rsGrupo->getNumLinhas() < 1 ) {

            $obDesoneracao->obRMONCredito->setCodCredito ( $this->getCodCredito() );
            $obDesoneracao->listarDesoneracaoCredito ( $rsLista );

            if ( $rsLista->getNumLinhas() < 1 ) {

                //--- excluir credito_moeda
                if ( !$obErro->ocorreu() ) {
                    $this->obTMONCreditoMoeda->setDado( "cod_credito", $this->getCodCredito() );
                    $this->obTMONCreditoMoeda->setDado( "cod_natureza", $this->getCodNatureza() );
                    $this->obTMONCreditoMoeda->setDado( "cod_genero", $this->getCodGenero() );
                    $this->obTMONCreditoMoeda->setDado( "cod_especie", $this->getCodEspecie() );
                    $this->obTMONCreditoMoeda->setDado( "cod_moeda", $this->getCodMoeda () );
                    $this->obTMONCreditoMoeda->setDado( "timestamp", $timestamp );
                    $obErro = $this->obTMONCreditoMoeda->exclusao( $boTransacao );

                    //--- excluir credito_norma
                    if ( !$obErro->ocorreu() ) {
                        $this->obTMONCreditoNorma->setDado( "cod_credito", $this->getCodCredito() );
                        $this->obTMONCreditoNorma->setDado( "cod_natureza", $this->getCodNatureza() );
                        $this->obTMONCreditoNorma->setDado( "cod_genero", $this->getCodGenero() );
                        $this->obTMONCreditoNorma->setDado( "cod_especie", $this->getCodEspecie() );
                        $this->obTMONCreditoNorma->setDado( "cod_norma", $this->getCodNorma () );
                        $this->obTMONCreditoNorma->setDado( "timestamp", $timestamp );
                        $obErro = $this->obTMONCreditoNorma->exclusao( $boTransacao );
                        }

                    //--- excluir credito_indicador
                    if ( !$obErro->ocorreu() ) {
                        $this->obTMONCreditoIndicador->setDado( "cod_credito", $this->getCodCredito() );
                        $this->obTMONCreditoIndicador->setDado( "cod_natureza", $this->getCodNatureza() );
                        $this->obTMONCreditoIndicador->setDado( "cod_genero", $this->getCodGenero() );
                        $this->obTMONCreditoIndicador->setDado( "cod_especie", $this->getCodEspecie() );
                        $this->obTMONCreditoIndicador->setDado( "cod_indicador", $this->getCodIndicador() );
                        $this->obTMONCreditoIndicador->setDado( "timestamp", $timestamp );
                        $obErro = $this->obTMONCreditoIndicador->exclusao( $boTransacao );
                    }

                    //--- excluir credito_acrescimo
                    if ( !$obErro->ocorreu() ) {
                        include_once ( CAM_GT_MON_NEGOCIO."RMONCreditoAcrescimo.class.php");
                        $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
                        $obRMONCreditoAcrescimo->setCodCredito ( $this->getCodCredito() );
                        $obErro = $obRMONCreditoAcrescimo->excluirCreditoAcrescimo();

                        //TA EXCLUINDO PELO COD_CREDITO, TODOS VINCULADOS A ELE
                    }

                    //--- excluir credito carteira
                    if ( !$obErro->ocorreu() ) {
                        $this->obTMONCreditoCarteira->setDado( "cod_credito", $this->getCodCredito() );
                        $this->obTMONCreditoCarteira->setDado( "cod_natureza", $this->getCodNatureza() );
                        $this->obTMONCreditoCarteira->setDado( "cod_genero", $this->getCodGenero() );
                        $this->obTMONCreditoCarteira->setDado( "cod_especie", $this->getCodEspecie() );
                        $obErro = $this->obTMONCreditoCarteira->exclusao ( $boTransacao );
                    }

                    if ( !$obErro->ocorreu() ) {
                        $this->obTMONCreditoContaCorrente->setDado( "cod_credito", $this->getCodCredito() );
                        $this->obTMONCreditoContaCorrente->setDado( "cod_natureza", $this->getCodNatureza() );
                        $this->obTMONCreditoContaCorrente->setDado( "cod_genero", $this->getCodGenero() );
                        $this->obTMONCreditoContaCorrente->setDado( "cod_especie", $this->getCodEspecie() );
                        $obErro = $this->obTMONCreditoContaCorrente->exclusao( $boTransacao );
                    }

                    $obTMONRegraDesoneracaoCredito = new TMONRegraDesoneracaoCredito;
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_credito", $this->getCodCredito() );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_natureza", $this->getCodNatureza() );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_genero", $this->getCodGenero() );
                    $obTMONRegraDesoneracaoCredito->setDado( "cod_especie", $this->getCodEspecie() );
                    $obTMONRegraDesoneracaoCredito->exclusao( $boTransacao );

                    //--- excluir credito (principal)
                    if ( !$obErro->ocorreu() ) {
                        //insercao na tabela CREDITO
                        $this->obTMONCredito->setDado( "cod_credito", $this->getCodCredito() );
                        $this->obTMONCredito->setDado( "cod_natureza", $this->getCodNatureza() );
                        $this->obTMONCredito->setDado( "cod_genero", $this->getCodGenero() );
                        $this->obTMONCredito->setDado( "cod_especie", $this->getCodEspecie() );
                        $this->obTMONCredito->setDado( "descricao_credito", $this->getDescricao() );
                        $obErro = $this->obTMONCredito->exclusao( $boTransacao );

                    }
                }
            } else {  // se achou registro na tabela desoneracao, Exibe Aviso.
                $obErro->setDescricao ("Crédito vinculado a um registro de 'Desoneração'");
            }
        } else {  // se achou registro na tabela creditos grupo, Exibe Aviso.
            $obGrupo->setCodGrupo( $_REQUEST['inCodGrupo'] );
            $obGrupo->consultarGrupo();
            $descricao = $obGrupo->getCodGrupo().".".$obGrupo->getExercicio()." - ".$obGrupo->getDescricao();
            $obErro->setDescricao ("Crédito ".$stMaskara." pertence ao grupo ".$descricao." ! Exclusão não permitida.");
        }
    } else {  // se achou registro na tabela calculo, Exibe Aviso.
        $obErro->setDescricao ("Crédito ".$stMaskara." possui cálculos efetuados! Exclusão não permitida.");
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONCredito );

    return $obErro;
}

/**
    * Alterar os dados referentes a Credito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function AlterarCredito($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = new Erro;
    $timestamp = date('Y-m-d h:m:s');

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if ( $this->VerificaNomeCredito() ) {
            $obErro->setDescricao ("Nome de Crédito já utilizado no sistema (". $this->getDescricao() .")");
        }

         if ( !$obErro->ocorreu() ) {
            $this->obTMONCredito->setDado( "cod_credito", $this->getCodCredito() );
            $this->obTMONCredito->setDado( "cod_natureza", $this->getCodNatureza() );
            $this->obTMONCredito->setDado( "cod_genero", $this->getCodGenero() );
            $this->obTMONCredito->setDado( "cod_especie", $this->getCodEspecie() );
            $this->obTMONCredito->setDado( "cod_convenio", $this->getCodConvenio() );
            $this->obTMONCredito->setDado( "descricao_credito", $this->getDescricao() );

            $obErro = $this->obTMONCredito->alteracao( $boTransacao );

        }

        $obTMONRegraDesoneracaoCredito = new TMONRegraDesoneracaoCredito;
        $obTMONRegraDesoneracaoCredito->setDado( "cod_credito", $this->getCodCredito() );
        $obTMONRegraDesoneracaoCredito->setDado( "cod_natureza", $this->getCodNatureza() );
        $obTMONRegraDesoneracaoCredito->setDado( "cod_genero", $this->getCodGenero() );
        $obTMONRegraDesoneracaoCredito->setDado( "cod_especie", $this->getCodEspecie() );
        $obTMONRegraDesoneracaoCredito->exclusao( $boTransacao );

        if ($this->stCodFuncaoDesoneracao) {
            $arCodFuncao = explode( ".", $this->stCodFuncaoDesoneracao );
            $obTMONRegraDesoneracaoCredito->setDado( "cod_modulo", $arCodFuncao[0] );
            $obTMONRegraDesoneracaoCredito->setDado( "cod_biblioteca", $arCodFuncao[1] );
            $obTMONRegraDesoneracaoCredito->setDado( "cod_funcao", $arCodFuncao[2] );
            $obTMONRegraDesoneracaoCredito->inclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $stFiltro = " WHERE cod_credito = ".$this->getCodCredito()." AND cod_natureza = ".$this->getCodNatureza()." AND cod_genero = ".$this->getCodGenero()." AND cod_especie = ".$this->getCodEspecie();
            $this->obTMONCreditoContaCorrente->recuperaTodos( $rsContas, $stFiltro, NULL, $boTransacao );

            $this->obTMONCreditoContaCorrente->setDado( "cod_agencia", $this->inCodAgencia );
            $this->obTMONCreditoContaCorrente->setDado( "cod_banco", $this->inCodBanco );
            $this->obTMONCreditoContaCorrente->setDado( "cod_conta_corrente", $this->inCodConta );
            $this->obTMONCreditoContaCorrente->setDado( "cod_convenio", $this->getCodConvenio() );

            $this->obTMONCreditoContaCorrente->setDado( "cod_credito", $this->getCodCredito() );
            $this->obTMONCreditoContaCorrente->setDado( "cod_natureza", $this->getCodNatureza() );
            $this->obTMONCreditoContaCorrente->setDado( "cod_genero", $this->getCodGenero() );
            $this->obTMONCreditoContaCorrente->setDado( "cod_especie", $this->getCodEspecie() );

            if ( $rsContas->Eof() )
                $obErro = $this->obTMONCreditoContaCorrente->inclusao( $boTransacao );
            else
                $obErro = $this->obTMONCreditoContaCorrente->alteracao( $boTransacao );
        }

        if ( !$obErro->ocorreu() && $this->getCodCarteira() ) {
            //alterando dados na tabela monetario.credito_carteira
            $this->obTMONCreditoCarteira->setDado( "cod_credito", $this->getCodCredito() );
            $this->obTMONCreditoCarteira->setDado( "cod_natureza", $this->getCodNatureza() );
            $this->obTMONCreditoCarteira->setDado( "cod_genero", $this->getCodGenero() );
            $this->obTMONCreditoCarteira->setDado( "cod_especie", $this->getCodEspecie() );
            $this->obTMONCreditoCarteira->setDado( "cod_carteira", $this->getCodCarteira() );
            $this->obTMONCreditoCarteira->setDado( "cod_convenio", $this->getCodConvenio() );

            $obErro = $this->obTMONCreditoCarteira->alteracao( $boTransacao );
        }

        if ( $this->getCodIndicador () ) {
            if ( !$obErro->ocorreu() ) {
                //insercao na tabela CREDITO_INDICADOR
                $this->obTMONCreditoIndicador->setDado( "cod_credito", $this->getCodCredito() );
                $this->obTMONCreditoIndicador->setDado( "cod_natureza", $this->getCodNatureza() );
                $this->obTMONCreditoIndicador->setDado( "cod_genero", $this->getCodGenero() );
                $this->obTMONCreditoIndicador->setDado( "cod_especie", $this->getCodEspecie() );
                $this->obTMONCreditoIndicador->setDado( "cod_indicador", $this->getCodIndicador() );
                $this->obTMONCreditoIndicador->setDado( "timestamp", $timestamp );
                $obErro = $this->obTMONCreditoIndicador->inclusao( $boTransacao );

            }
        } elseif ( $this->getCodMoeda () ) {
            if ( !$obErro->ocorreu() ) {
                //insercao na tabela CREDITO_MOEDA
                $this->obTMONCreditoMoeda->setDado( "cod_credito", $this->getCodCredito() );
                $this->obTMONCreditoMoeda->setDado( "cod_natureza", $this->getCodNatureza() );
                $this->obTMONCreditoMoeda->setDado( "cod_genero", $this->getCodGenero() );
                $this->obTMONCreditoMoeda->setDado( "cod_especie", $this->getCodEspecie() );
                $this->obTMONCreditoMoeda->setDado( "cod_moeda", $this->getCodMoeda () );
                $this->obTMONCreditoMoeda->setDado( "timestamp", $timestamp );
                $obErro = $this->obTMONCreditoMoeda->inclusao( $boTransacao );

            }
        }

       if ( !$obErro->ocorreu() ) {
            $arNormas = $this->getArCodNorma();
            $this->obTMONCreditoNorma->setDado( "cod_credito", $this->getCodCredito() );
            $this->obTMONCreditoNorma->setDado( "cod_natureza", $this->getCodNatureza() );
            $this->obTMONCreditoNorma->setDado( "cod_genero", $this->getCodGenero() );
            $this->obTMONCreditoNorma->setDado( "cod_especie", $this->getCodEspecie() );
            $obErro = $this->obTMONCreditoNorma->exclusao( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                for ( $inX=0; $inX<count($arNormas); $inX++ ) {
                    //insercao na tabela CREDITO_NORMA
                    $this->obTMONCreditoNorma->setDado( "cod_norma", $arNormas[$inX]["inCodNorma"] );
                    $this->obTMONCreditoNorma->setDado( "dt_inicio_vigencia", $arNormas[$inX]["dtVigenciaInicio"] );
                    $obErro = $this->obTMONCreditoNorma->inclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }

     if ( !$obErro->ocorreu() ) {
        //insercao na tabela CREDITO_ACRESCIMO
        include_once ( CAM_GT_MON_MAPEAMENTO."TMONCreditoAcrescimo.class.php");
        include_once ( CAM_GT_MON_NEGOCIO."RMONCreditoAcrescimo.class.php");

        $stFiltro = " WHERE mca.cod_credito = ".$this->getCodCredito()." AND mca.cod_genero = ".$this->getCodGenero()." AND mca.cod_especie = ".$this->getCodEspecie()." AND mca.cod_natureza = ".$this->getCodNatureza();
        $obTMONCreditoAcrescimo = new TMONCreditoAcrescimo;
        $obTMONCreditoAcrescimo->recuperaAcrescimosDoCreditoGF( $rsListaAcrescimo, $stFiltro, NULL, $boTransacao );

        $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
        $obRMONCreditoAcrescimo->setCodCredito ( $this->getCodCredito() );
        $obRMONCreditoAcrescimo->setCodGenero   ( $this->getCodGenero() );
        $obRMONCreditoAcrescimo->setCodNatureza ( $this->getCodNatureza() );
        $obRMONCreditoAcrescimo->setCodEspecie  ( $this->getCodEspecie() );

       //Primeiramente, os acréscimos vinculados no banco são removidos
        //Logo após, insere os novos dados trazidos pela $sessao->transf4['acrescimos']

        $NRegistros = count (  $this->getAcrescimos()  );
        if (  count (   $NRegistros ) > 0 ) {
            $newArAcrescimos = $this->getAcrescimos();

            //deletar todos acrescimos que nao estao na lista
            while ( !$rsListaAcrescimo->Eof() ) {
                $boNaLista = false;
                $cont = 0;
                while ($cont < $NRegistros) {
                    $regCod = $newArAcrescimos[$cont]['cod_acrescimo'];
                    $regTipo = $newArAcrescimos[$cont]['cod_tipo'];
                    if ( ($regCod == $rsListaAcrescimo->getCampo( "cod_acrescimo" )) && ($regTipo == $rsListaAcrescimo->getCampo( "cod_tipo" )) ) {
                        $boNaLista = true;
                        break;
                    }

                    $cont++;
                }

                if (!$boNaLista) {
                    if ( $rsListaAcrescimo->getCampo( "tipo_cpa" ) == 't' ) {
                        $obErro = new Erro;
                        $obErro->setDescricao("Acrescimo ".$rsListaAcrescimo->getCampo( "cod_acrescimo" )." está sendo utilizado pelo plano de conta ".$rsListaAcrescimo->getCampo( "cpa.cod_plano" ).". Nao pode ser excluido!");

                        return $obErro;
                    }else
                        if ( $rsListaAcrescimo->getCampo( "tipo_orc" ) == 't' ) {
                            $obErro = new Erro;
                            $obErro->setDescricao("Acrescimo ".$rsListaAcrescimo->getCampo( "cod_acrescimo" )." está sendo utilizado pela receita ".$rsListaAcrescimo->getCampo( "orc.cod_receita" ).". Nao pode ser excluido!");

                            return $obErro;
                        }

                    $obTMONCreditoAcrescimo->setDado( "cod_credito", $this->getCodCredito() );
                    $obTMONCreditoAcrescimo->setDado( "cod_natureza", $this->getCodNatureza() );
                    $obTMONCreditoAcrescimo->setDado( "cod_especie", $this->getCodEspecie() );
                    $obTMONCreditoAcrescimo->setDado( "cod_genero", $this->getCodGenero() );
                    $obTMONCreditoAcrescimo->setDado( "cod_tipo", $rsListaAcrescimo->getCampo( "cod_tipo" ) );
                    $obTMONCreditoAcrescimo->setDado( "cod_acrescimo", $rsListaAcrescimo->getCampo( "cod_acrescimo" ) );
                    $obTMONCreditoAcrescimo->exclusao( $boTransacao );
                }

                $rsListaAcrescimo->proximo();
            }

            $cont = 0;
            while ($cont < $NRegistros) {
                $regCod  = $newArAcrescimos[$cont]['cod_acrescimo'];
                $regTipo = $newArAcrescimos[$cont]['cod_tipo'];

                $obRMONCreditoAcrescimo->setCodAcrescimo ( $regCod );
                $obRMONCreditoAcrescimo->setCodTipo ( $regTipo );

                $obErro = $obRMONCreditoAcrescimo->ListarAcrescimosDoCredito ( $rsAcrescimos, $boTransacao );
                if ( !$obErro->ocorreu() && $rsAcrescimos->getNumLinhas() < 1 ) {
                    /*INSERE*/
                    $obRMONCreditoAcrescimo->incluirCreditoAcrescimo();
                }

                $cont++;
            }
      }//fim insercao dos creditos
  }
}
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONCredito );

    return $obErro;
}

/**
* Lista os Créditos
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCreditosUnicos(&$rsRecordSet, $boTransacao = "")
{
    $descricao = strToupper ( $this->getDescricao() ) ;

    $stFiltro = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n mc.cod_credito = '".$this->getCodCredito()."' AND ";
    }
    if ( $this->getCodEspecie() ) {
        $stFiltro .= " \n me.cod_especie = '".$this->getCodEspecie()."' AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " \n mg.cod_genero = '".$this->getCodGenero()."' AND ";
    }
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n mn.cod_natureza = '".$this->getCodNatureza()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " \n UPPER (mc.descricao_credito) like '%". $descricao ."%' AND ";
    }
    if ($stFiltro) {
        $stFiltro = "     WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = "\n ORDER BY apc.timestamp desc limit 1 ";
    $obErro = $this->obTMONCredito->recuperaRelacionamentoUnico( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
function listarCreditosPopUp(&$rsRecordSet, $boTransacao = "")
{
    $descricao = strToupper ( $this->getDescricao() ) ;

    $stFiltro = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n mc.cod_credito = '".$this->getCodCredito()."' AND ";
    }
    if ( $this->getCodEspecie() ) {
        $stFiltro .= " \n me.cod_especie = '".$this->getCodEspecie()."' AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " \n mg.cod_genero = '".$this->getCodGenero()."' AND ";
    }
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n mn.cod_natureza = '".$this->getCodNatureza()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " \n UPPER (mc.descricao_credito) like '%". $descricao ."%' AND ";
    }
    if ($stFiltro) {
        $stFiltro = "     WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = "\n ORDER BY mc.cod_credito";
    $obErro = $this->obTMONCredito->recuperaRelacionamentoPopUp( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarCreditosPopUpGF(&$rsRecordSet, $boTransacao = "")
{
    $descricao = strToupper ( $this->getDescricao() ) ;

    $stFiltro = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n mc.cod_credito = '".$this->getCodCredito()."' AND ";
    }
    if ( $this->getCodEspecie() ) {
        $stFiltro .= " \n me.cod_especie = '".$this->getCodEspecie()."' AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " \n mg.cod_genero = '".$this->getCodGenero()."' AND ";
    }
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n mn.cod_natureza = '".$this->getCodNatureza()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " \n UPPER (mc.descricao_credito) like '%". $descricao ."%' AND ";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " \n exercicio = ".$this->getExercicio()."::varchar  AND ";
    }
    if ( $this->getCodEntidade() ) {
        $stFiltro .= " \n cod_entidade = ".$this->getCodEntidade()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = "     WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = "\n ORDER BY mc.cod_credito";
    $obErro = $this->obTMONCredito->recuperaRelacionamentoPopUpGF( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Lista os Créditos
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCreditos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n mc.cod_credito = '".$this->getCodCredito()."' AND ";
    }
    if ( $this->getCodEspecie() ) {
        $stFiltro .= " \n me.cod_especie = '".$this->getCodEspecie()."' AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " \n mg.cod_genero = '".$this->getCodGenero()."' AND ";
    }
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n mn.cod_natureza = '".$this->getCodNatureza()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " \n upper (mc.descricao_credito) like upper ('%".$this->getDescricao()."%') AND ";
    }
    if ($this->getExercicio()) {
        $stFiltro .= " \n exercicio = ".$this->getExercicio()."  AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = "\n ORDER BY mc.cod_credito ";
    $obErro = $this->obTMONCredito->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function consultarCredito($boTransacao = "")
{
    $obErro = new Erro;
    if ( $this->getCodCredito() && $this->getCodEspecie() && $this->getCodGenero() &&  $this->getCodNatureza()  ) {
        $obErro = $this->listarCreditos( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() && ($rsRecordSet->getNumLinhas() >= 1) ) {
            $this->inCodCredito     = $rsRecordSet->getCampo("cod_credito"      );
            $this->stDescricao      = $rsRecordSet->getCampo("descricao_credito");
            $this->inCodNatureza    = $rsRecordSet->getCampo("cod_natureza"     );
            $this->stNomeNatureza   = $rsRecordSet->getCampo("nom_natureza"     );
            $this->inCodEspecie     = $rsRecordSet->getCampo("cod_especie"      );
            $this->stNomeEspecie    = $rsRecordSet->getCampo("nom_especie"      );
            $this->inCodGenero      = $rsRecordSet->getCampo("cod_genero"       );
            $this->stNomeGenero     = $rsRecordSet->getCampo("nom_genero"       );
            $this->obRNorma->setCodNorma($rsRecordSet->getCampo("cod_norma") );
        }
    } else {
        $obErro->setDescricao("Deve ser setada a chave da tabela monetario.credito ");
    }

    return $obErro;
}
function consultarCreditoPermissao($boTransacao = "")
{
    if ( $this->getCodCredito() ) {
        $stFiltro = "\r\n\t AND mc.cod_credito = ".$this->getCodCredito();
        $obErro = $this->obTMONCredito->recuperaPermissaoGrupo( $rsRecordSet, $stFiltro,'', $boTransacao );

        if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
            $this->inCodCredito     = $rsRecordSet->getCampo("cod_credito"      );
            $this->stDescricao      = $rsRecordSet->getCampo("descricao_credito");
            $this->inCodNatureza    = $rsRecordSet->getCampo("cod_natureza"     );
            $this->inCodEspecie     = $rsRecordSet->getCampo("cod_especie"      );
            $this->inCodGenero      = $rsRecordSet->getCampo("cod_genero"       );
            $this->obRNorma->setCodNorma($rsRecordSet->getCampo("cod_norma") );
        }
    }

    return $obErro;
}

function consultarMascaraCredito($boTransacao = "")
{
    $obErro = $this->obTMONCredito->recuperaMascaraCredito( $rsRecordSet, '','', $boTransacao );

    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->stMascaraCredito = $rsRecordSet->getCampo("mascara_credito");
    }

    return $obErro;
}
/*
*****************************************************************************
*/
/**
    * Inclui os dados referentes a Especie-Credito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirEspecie($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obMONEspecieCredito->proximoCod( $this->inCodEspecie, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTMONEspecieCredito->setDado( "cod_especie"     , $this->getCodEspecie()    );
            $this->obTMONEspecieCredito->setDado( "cod_genero"      , $this->getCodGenero()     );
            $this->obTMONEspecieCredito->setDado( "cod_natureza"    , $this->getCodNatureza()   );
            $this->obTMONEspecieCredito->setDado( "nom_especie"     , $this->getNomeEspecie()   );
            $obErro = $this->obTMONEspecieCredito->inclusao();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONEspecieCredito);

    return $obErro;
}
/**
    * Inclui os dados referentes a Especie-Credito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirGenero($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obMONGeneroCredito->proximoCod( $this->inCodGenero, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTMONGeneroCredito->setDado( "cod_genero"      , $this->getCodGenero()     );
            $this->obTMONGeneroCredito->setDado( "cod_natureza"    , $this->getCodNatureza()   );
            $this->obTMONGeneroCredito->setDado( "nom_genero"      , $this->getNomeGenero()   );
            $obErro = $this->obTMONGeneroCredito->inclusao();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONGeneroCredito );

    return $obErro;
}
/**
    * Inclui os dados referentes a Especie-Credito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirNatureza($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obMONNaturezaCredito->proximoCod( $this->inCodNatureza, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTMONNaturezaCredito->setDado( "cod_natureza"    , $this->getCodNatureza()   );
            $this->obTMONNaturezaCredito->setDado( "nom_natureza"    , $this->getNomeEspecie()   );
            $obErro = $this->obTMONNaturezaCredito->inclusao();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONNaturezaCredito );

    return $obErro;
}
/*
<<<<<<< RMONCredito.class.php
=======
//function refGrupoCredito() {
>>>>>>> 1.13*/
function refGrupoCredito(&$obRARRGrupo)
{
    $this->roRARRGrupo = &$obRARRGrupo;
}

/**
* Lista os registros de Tipos de NATUREZA
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarNatureza(&$rsRecordSet, $boTransacao = '')
{
  include_once ( CAM_GT_MON_MAPEAMENTO."TMONNaturezaCredito.class.php");
  $this->obTMONNatureza = new TMONNaturezaCredito;

    $stOrder = "";
    $obErro = $this->obTMONNatureza->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}//fim lista de NATUREZA DE CREDITO

/**
* Lista os registros de GENEROS
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarGenero(&$rsRecordSet, $boTransacao = '')
{
    include_once ( CAM_GT_MON_MAPEAMENTO."TMONGeneroCredito.class.php");
    $this->obTMONGenero = new TMONGeneroCredito;

    $stFiltro = '';
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n cod_natureza = '".$this->getCodNatureza()."' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = "\n ORDER BY mc.cod_genero ";

    $stOrder = "";
    $obErro = $this->obTMONGenero->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}//fim lista de GENERO DE CREDITO

/**
* Lista os registros de GENEROS
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarGeneroNatureza(&$rsRecordSet, $boTransacao = '')
{
    include_once ( CAM_GT_MON_MAPEAMENTO."TMONGeneroCredito.class.php");
    $this->obTMONGenero = new TMONGeneroCredito;

    $stFiltro = '';

    if ( $this->getCodNatureza() ) {
        //$stFiltro .= " \n cod_natureza = '".$this->getCodNatureza()."' AND ";
        $stFiltro .= " \n gc.cod_natureza = ".$this->getCodNatureza() . " AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = "\n ORDER BY mc.cod_genero ";

    $stOrder = "";
    $obErro = $this->obTMONGenero->recuperaGeneroNatureza( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}//fim lista de GENERO DE CREDITO

/**
* Lista os registros de ESPECIES
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarEspecie(&$rsRecordSet, $boTransacao = '')
{
    include_once ( CAM_GT_MON_MAPEAMENTO."TMONEspecieCredito.class.php");
    $this->obTMONEspecie = new TMONEspecieCredito;

    $stFiltro = '';
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n cod_natureza = '".$this->getCodNatureza()."' AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " \n cod_genero = '".$this->getCodGenero()."' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = "\n ORDER BY mc.cod_especie ";

    $stOrder = "";
    $obErro = $this->obTMONEspecie->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}//fim lista de ESPECIES DE CREDITO

/**
    * Verifica se o NOME DO CREDITO a ser incluido ja nao existe
    * @access Public
    * @param  Object $rsBanco Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function verificaNomeCredito($boTransacao = "")
{
   $obErro = $this->obTMONCredito->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    $cont =0;
    $achou = false;
    $valores = Array ();
    while ($cont < $rsRecordSet->getNumLinhas()) {

        $valores[$cont] = strtoupper ($rsRecordSet->getCampo("descricao_credito"));
        $codigos[$cont] = $rsRecordSet->getCampo("cod_credito");

        if ( $valores[$cont] == strtoupper ($this->stDescricao) &&  $codigos[$cont] != $this->inCodCredito ) {
            $achou = true; break;
        }

        $cont++;
        $rsRecordSet->proximo();
    }

    //return $obErro;
    return $achou;
}

function buscaNormaCredito(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = $stOrder = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n cm.cod_credito = '".$this->getCodCredito()."' AND ";
    }
    if ( $this->getCodNorma() ) {
        $stFiltro .= " \n cm.cod_norma = '".$this->getCodNorma()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE " . substr ( $stFiltro, 0 , strlen ($stFiltro)-4) ." \n";
    }
    $stOrder = " ORDER BY timestamp DESC LIMIT 1";

    $obErro = $this->obTMONCreditoNorma->recuperaRelacionamentoBuscaNorma( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

function buscaMoedaCredito(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = $stOrder = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n cm.cod_credito = ".$this->getCodCredito()." AND ";
    }
    if ( $this->getCodMoeda() ) {
        $stFiltro .= " \n mo.cod_moeda = ".$this->getCodMoeda()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE " . substr ( $stFiltro, 0 , strlen ($stFiltro)-4) ." \n";
    }
    $stOrder = " ORDER BY timestamp DESC LIMIT 1";

    $obErro = $this->obTMONCreditoMoeda->recuperaMoedaCredito( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

function buscaIndicadorCredito(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = $stOrder = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n ci.cod_credito = ".$this->getCodCredito()." AND ";
    }
    if ( $this->getCodIndicador() ) {
        $stFiltro .= " \n ie.cod_indicador = ".$this->getCodIndicador()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE " . substr ( $stFiltro, 0 , strlen ($stFiltro)-4) ." \n";
    }
    $stOrder = " ORDER BY timestamp DESC LIMIT 1";

    $obErro = $this->obTMONCreditoIndicador->recuperaIndicadorCredito( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}
}//fim da classe
