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
    * Regra de negócio para Desoneração
    * Data de Criação   :  27/05/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @subpackage Regras
    * @package Urbem

    * $Id: RARRDesoneracao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03
*/

/*
$Log$
Revision 1.16  2007/03/01 13:44:04  rodrigo
Bug #8440#

Revision 1.15  2006/11/23 10:27:04  cercato
bug #7539#

Revision 1.14  2006/11/16 16:49:35  cercato
correcao para calculo/lancamento individual cadastro economico.

Revision 1.13  2006/10/02 09:11:52  domluc
#6973#

Revision 1.12  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.11  2006/09/15 10:48:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_ARR_MAPEAMENTO."TARRDesoneracao.class.php"              );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRAtributoDesoneracao.class.php"      );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRAtributoDesoneracaoValor.class.php" );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRTipoDesoneracao.class.php"          );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRDesonerado.class.php"               );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRDesoneradoImovel.class.php"         );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRDesoneradoCadEconomico.class.php"   );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRLancamentoConcedeDesoneracao.class.php" );
include_once (CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"               );
include_once (CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"                       );
include_once (CAM_GT_MON_NEGOCIO."RMONCredito.class.php"                     );
include_once (CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php"          );
include_once (CAM_GA_ADM_NEGOCIO."RFuncao.class.php"                         );
include_once (CAM_GA_CGM_NEGOCIO."RCGM.class.php"                            );

class RARRDesoneracao
{
    /**
        * @access Private
        * @param Integer
    */
    public $inInscricaoEconomica;
    /**
        * @access Private
        * @param Integer
    */
    public $inInscricaoImovel;
    /**
        * @access Private
        * @param Integer
    */
    public $inCodigo;
    /**
        * @access Private
        * @param String
    */
    public $stTipo;
    /**
        * @access Private
        * @param Date
    */
    public $dtInicio;
    /**
        * @access Private
        * @param Date
    */
    public $dtTermino;
    /**
        * @access Private
        * @param Date
    */
    public $dtExpiracao;
    /**
        * @access Private
        * @param Date
    */
    public $dtProrrogacao;
    /**
        * @access Private
        * @param Boolean
    */
    public $boProrrogavel;
    /**
        * @access Private
        * @param Boolean
    */
    public $boRevogavel;
    /**
        * @access Private
        * @param Object
    */
    public $roUltimaNorma;
    /**
        * @access Private
        * @param Integer
    */
    public $inCodigoTipo;
    /**
        * @access Private
        * @param Date
    */
    public $dtConcessao;
    /**
        * @access Private
        * @param Date
    */
    public $dtRevogacao;
    /**
        * @access Private
        * @param Object
    */
    public $obRMONIndicadorEconomico;
    /**
        * @access Private
        * @param Integer
    */
    public $inOcorrencia;
    /**
        * @access Private
        * @param Integer
    */
    public $inCodCalculo;
    /**
        * @access Private
        * @param Integer
    */
    public $inCodLancamento;

    /**
        @access Public
        @param String $valor
    */
    public function setOcorrencia($valor)
    {
        $this->inOcorrencia = $valor;
    }

    /**
        @access Public
        @param String $valor
    */
    public function setCodigo($valor)
    {
        $this->inCodigo = $valor;
    }

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setInscricaoImovel($valor)
    {
        $this->inInscricaoImovel = $valor;
    }

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setInscricaoEconomica($valor)
    {
        $this->inInscricaoEconomica = $valor;
    }

    /**
        * @access Public
        * @param String $valor
    */
    public function setTipo($valor)
    {
        $this->stTipo = $valor;
    }
    /**
        * @access Public
        * @param Date $valor
    */
    public function setInicio($valor)
    {
        $this->dtInicio = $valor;
    }
    /**
        * @access Public
        * @param Date $valor
    */
    public function setProrrogacao($valor)
    {
        $this->dtProrrogacao = $valor;
    }
    /**
        @access Public
        @param Date $valor
    */
    public function setTermino($valor)
    {
        $this->dtTermino = $valor;
    }
    /**
        @access Public
        @param Date $valor
    */
    public function setConcessao($valor)
    {
        $this->dtConcessao = $valor;
    }
    /**
        @access Public
        @param Date $valor
    */
    public function setRevogacao($valor)
    {
        $this->dtRevogacao = $valor;
    }
    /**
        @access Public
        @param Date $valor
    */
    public function setExpiracao($valor)
    {
        $this->dtExpiracao = $valor;
    }
    /**
        @access Public
        @param Boolean $valor
    */
    public function setProrrogavel($valor)
    {
        $this->boProrrogavel = $valor;
    }
    /**
        @access Public
        @param Boolean $valor
    */
    public function setRevogavel($valor)
    {
        $this->boRevogavel = $valor;
    }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoTipo($valor)
    {
        $this->inCodigoTipo = $valor;
    }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoLancamento($valor)
    {
        $this->inCodLancamento = $valor;
    }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoCalculo($valor)
    {
        $this->inCodCalculo = $valor;
    }

    /**
        * @access Public
        * @return Integer
    */
    public function getOcorrencia()
    {
        return $this->inOcorrencia;
    }

    /**
        * @access Public
        * @return Integer
    */
    public function getInscricaoImovel()
    {
        return $this->inInscricaoImovel;
    }

    /**
        * @access Public
        * @return Integer
    */
    public function getInscricaoEconomica()
    {
        return $this->inInscricaoEconomica;
    }

    /**
        * @access Public
        * @return String
    */
    public function getCodigo()
    {
        return $this->inCodigo;
    }
    /**
        * @access Public
        * @return String
    */
    public function getTipo()
    {
        return $this->stTipo;
    }
    /**
        * @access Public
        a
        * @return Date
    */
    public function getInicio()
    {
        return $this->dtInicio;
    }
    /**
        * @access Public
        * @return Date
    */
    public function getTermino()
    {
        return $this->dtTermino;
    }
    /**
        * @access Public
        * @return Date
    */
    public function getConcessao()
    {
        return $this->dtConcessao;
    }
     /**
        * @access Public
        * @return Date
    */
    public function getProrrogacao()
    {
        return $this->dtProrrogacao;
    }
     /**
        * @access Public
        * @return Date
    */
    public function getExpiracao()
    {
        return $this->dtExpiracao;
    }
     /**
        * @access Public
        * @return Date
    */
    public function getRevogacao()
    {
        return $this->dtRevogacao;
    }
    /**
        * @access Public
        * @return Boolean
    */
    public function getProrrogavel()
    {
        return $this->boProrrogavel;
    }
    /**
        * @access Public
        * @return Boolean
    */
    public function getRevogavel()
    {
        return $this->boRevogavel;
    }
    /**
        * @access Public
        * @return Integer
    */
    public function getCodigoTipo()
    {
        return $this->inCodigoTipo;
    }
    /**
        * @access Public
        * @return Integer
    */
    public function getCodigoLancamento()
    {
        return $this->inCodLancamento;
    }
    /**
        * @access Public
        * @return Integer
    */
    public function getCodigoCalculo()
    {
        return $this->inCodCalculo;
    }

    /**
        * Metodo construtor
        * @access Private
    */
    public function RARRDesoneracao()
    {
        $this->obTransacao           = new Transacao;
        $this->obRCadastroDinamico   = new RCadastroDinamico;
        $this->obTARRDesoneracao     = new TARRDesoneracao;
        $this->obTARRDesonerado      = new TARRDesonerado;
        $this->obTARRTipoDesoneracao = new TARRTipoDesoneracao;
        $this->obTARRLancamentoConcedeDesoneracao = new TARRLancamentoConcedeDesoneracao;
        $this->obRCadastroDinamico->setPersistenteAtributos( new TARRAtributoDesoneracao );
        $this->obRCadastroDinamico->setPersistenteValores  ( new TARRAtributoDesoneracaoValor );
        $this->obRCadastroDinamico->setCodCadastro( 3 );
        $this->obRFuncao             = new RFuncao;
        $this->obRCGM                = new RCGM;
        $this->obRMONCredito         = new RMONCredito;
        $this->obRMONIndicadorEconomico = new RMONIndicadorEconomico;
        $this->arRNorma              = array();
    }

    /**
        * Adiciona um objeto de Norma ( Prorrogacao ) no array de Prorrogacao
        * @access Public
    */
    public function addNorma()
    {
        $this->arRNorma[] = new RNorma( $this );
        $this->roUltimaNorma = &$this->arRNorma[ count($this->arRNorma) - 1 ];
    }

    /**
        * Recupera uma lista de desoneração de acordo com o filtro
        * @access Public
        * @param RecordSet, Transacao
        * @return RecordSet
    */
    public function listarDesoneracaoCredito(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getCodigoTipo() ) {
            $stFiltro .= " AND TD.COD_TIPO_DESONERACAO = ".$this->getCodigoTipo()."  ";
        }
        if ( $this->getCodigo() ) {
            $stFiltro .= " AND DE.COD_DESONERACAO = ".$this->getCodigo()."  ";
        }
        if ( $this->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " AND CR.COD_CREDITO = ".$this->obRMONCredito->getCodCredito()."  ";
        }
        if ( $this->obRMONCredito->getDescricao()) {
            $stFiltro .= " AND CR.DESCRICAO_CREDITO LIKE '%".$this->obRMONCredito->getDescricao()."%'  ";
        }

        if ( $this->obRMONCredito->getDescricao()) {
            $stFiltro .= " AND CR.DESCRICAO_CREDITO LIKE '%".$this->obRMONCredito->getDescricao()."%'  ";
        }

        if ( $this->obRCGM->getNumCGM() ) {
            $stFiltro .= " AND AD.NUMCGM = ".$this->obRCGM->getNumCGM()."  ";
        }

        $stOrdem = " ORDER BY DE.COD_DESONERACAO ";
        $obErro = $this->obTARRDesoneracao->recuperaDesoneracaoCredito( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarDesoneracaoCreditoPopUP(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getCodigoTipo() ) {
            $stFiltro .= " AND TD.COD_TIPO_DESONERACAO = ".$this->getCodigoTipo()."  ";
        }
        if ( $this->getCodigo() ) {
            $stFiltro .= " AND DE.COD_DESONERACAO = ".$this->getCodigo()."  ";
        }
        if ( $this->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " AND CR.COD_CREDITO = ".$this->obRMONCredito->getCodCredito()."  ";
        }
        if ( $this->obRMONCredito->getDescricao()) {
            $stFiltro .= " AND CR.DESCRICAO_CREDITO LIKE '%".$this->obRMONCredito->getDescricao()."%'  ";
        }

        if ( $this->obRMONCredito->getDescricao()) {
            $stFiltro .= " AND CR.DESCRICAO_CREDITO LIKE '%".$this->obRMONCredito->getDescricao()."%'  ";
        }

        if ( $this->obRCGM->getNumCGM() ) {
            $stFiltro .= " AND AD.NUMCGM = ".$this->obRCGM->getNumCGM()."  ";
        }

        $stOrdem = " ORDER BY DE.COD_DESONERACAO ";
        $obErro = $this->obTARRDesoneracao->recuperaDesoneracaoCreditoPopup( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarDesoneracaoCredito2(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";

        if ($this->boRevogavel  == 't') {
        $stFiltro .= " AND DE.REVOGAVEL = 't' ";
        }
        if ($this->boProrrogavel  == 't') {
        $stFiltro .= " AND DE.PRORROGAVEL = 't' ";
        }
        if ( $this->getCodigoTipo() ) {
            $stFiltro .= " AND TD.COD_TIPO_DESONERACAO = ".$this->getCodigoTipo()."  ";
        }
        if ( $this->getCodigo() ) {
            $stFiltro .= " AND DE.COD_DESONERACAO = ".$this->getCodigo()."  ";
        }
        if ( $this->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " AND CR.COD_CREDITO = ".$this->obRMONCredito->getCodCredito()."  ";
        }
        if ( $this->obRMONCredito->getDescricao()) {
            $stFiltro .= " AND CR.DESCRICAO_CREDITO LIKE '%".$this->obRMONCredito->getDescricao()."%'  ";
        }

        if ( $this->obRMONCredito->getDescricao()) {
            $stFiltro .= " AND CR.DESCRICAO_CREDITO LIKE '%".$this->obRMONCredito->getDescricao()."%'  ";
        }

        if ( $this->obRCGM->getNumCGM() ) {
            $stFiltro .= " AND AD.NUMCGM = ".$this->obRCGM->getNumCGM()."  ";
        }else
            $stFiltro .= " AND AD.NUMCGM IS NOT NULL  ";

        $stOrdem = " ORDER BY DE.COD_DESONERACAO ";
        $obErro = $this->obTARRDesoneracao->recuperaDesoneracaoCredito( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
                 #$this->obTARRDesoneracao->debug();

        return $obErro;
    }

    /**
        * Recupera a ocorrencia de desoneracao para um CGM
        * @access Public
        * @param RecordSet, Transacao
        * @return RecordSet
    */
    public function consultaOcorrenciaDesonerado(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getCodigo() ) {
            $stFiltro .= " cod_desoneracao = ".$this->getCodigo()." AND ";
        }

        if ( $this->obRCGM->getNumCGM() ) {
            $stFiltro .= " numcgm = ".$this->obRCGM->getNumCGM()." AND ";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, -4 );
        }

        $stOrdem = "ORDER BY ocorrencia DESC ";
        $obErro = $this->obTARRDesonerado->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Lista os tipos de desoneração
        * @access Public
    */
    public function listarTipoDesoneracao(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->inCodigoTipo) {
            $stFiltro = " COD_TIPO_DESONERACAO = ".$this->inCodigoTipo." AND ";
        }

        if ($this->stTipo) {
            $stFiltro = " DESCRICAO LIKE UPPER( '".$this->stTipo."%')";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        }

        $stOrdem = " ORDER BY COD_TIPO_DESONERACAO ";

        $obErro = $this->obTARRTipoDesoneracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Verifica se o Cgm, Inscricao Imobiliaria ou Economica esta desonerado
        * @access Public
    */
    public function consultaDesonerado(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->inInscricaoEconomica) {
            $stFiltro = " AND DE.inscricao_economica = ".$this->inInscricaoEconomica;
        }
        if ($this->inInscricaoImovel) {
            $stFiltro = " AND DI.inscricao_municipal = ".$this->inInscricaoImovel;
        }
        if ( $this->obRCGM->getNumCGM() ) {
        }
        $obErro = $this->obTARRDesonerado->recuperaDesoneracao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Consultar desoneração
        * @access Public
        * @param Transacao
    */
    public function consultarDesoneracao($boTransacao = "")
    {
        if ( $this->getCodigo() ) {
            $obErro = $this->listarDesoneracaoCredito( $rsRecordSet, $boTransacao );
            if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
                $this->obRMONCredito->setCodCredito ( $rsRecordSet->getCampo( "cod_credito"     ) );
                $this->obRMONCredito->setCodEspecie ( $rsRecordSet->getCampo( "cod_especie"     ) );
                $this->obRMONCredito->setCodGenero  ( $rsRecordSet->getCampo( "cod_genero"      ) );
                $this->obRMONCredito->setCodNatureza( $rsRecordSet->getCampo( "cod_natureza"    ) );
                $obErro = $this->obRMONCredito->consultarCredito( $boTransacao );
                if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
                    $this->inOcorrencia = $rsRecordSet->getCampo( "ocorrencia" );
                    $this->setCodigoTipo  ( $rsRecordSet->getCampo( "cod_tipo_desoneracao" ) );
                    $this->setInicio      ( $rsRecordSet->getCampo( "data_inicio"          ) );
                    $this->setTermino     ( $rsRecordSet->getCampo( "data_termino"         ) );
                    $this->setExpiracao   ( $rsRecordSet->getCampo( "data_expiracao"       ) );
                    $this->setConcessao   ( $rsRecordSet->getCampo( "data_concessao"       ) );
                    $this->setProrrogacao ( $rsRecordSet->getCampo( "data_prorrogacao"     ) );
                    $this->setRevogacao   ( $rsRecordSet->getCampo( "data_revogacao"       ) );
                    $this->setProrrogavel ( $rsRecordSet->getCampo( "prorrogavel"          ) );
                    $this->setRevogavel   ( $rsRecordSet->getCampo( "revogavel"            ) );
                    $this->obRCGM->setNumCGM( $rsRecordSet->getCampo( "numcgm"             ) );
                    $this->obRCGM->setNomCGM( $rsRecordSet->getCampo( "nom_cgm"            ) );
                    $this->addNorma();
                    $this->roUltimaNorma->setCodNorma( $rsRecordSet->getCampo( "fundamentacao_legal" ) );
                    $this->setTipo        ( $rsRecordSet->getCampo( "descricao_tipo"       ) );
                }
            }
        }

        return $obErro;
    }

    /**
        * Prorrogar desoneração
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function prorrogarDesoneracao($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRDesonerado->setDado( "ocorrencia"      , $this->inOcorrencia );
            $this->obTARRDesonerado->setDado( "cod_desoneracao" , $this->inCodigo );
            $this->obTARRDesonerado->setDado( "numcgm"          , $this->obRCGM->getNumCGM() );
            $this->obTARRDesonerado->setDado( "data_concessao"  , $this->dtConcessao );
            $this->obTARRDesonerado->setDado( "data_prorrogacao", $this->dtProrrogacao );
            if ($this->dtRevogacao) {
                $this->obTARRDesonerado->setDado( "data_revogacao"  , $this->dtRevogacao );
            } else {
                $this->obTARRDesonerado->setDado( "data_revogacao"  , null );
            }
            $obErro = $this->obTARRDesonerado->alteracao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRDesonerado );

        return $obErro;
    }

    /**
        * Revogar desoneração
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function revogarDesoneracao($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRDesonerado->setDado( "ocorrencia"      , $this->inOcorrencia );
            $this->obTARRDesonerado->setDado( "cod_desoneracao" , $this->inCodigo );
            $this->obTARRDesonerado->setDado( "numcgm"          , $this->obRCGM->getNumCGM() );
            $this->obTARRDesonerado->setDado( "data_concessao"  , $this->dtConcessao );
            if ($this->dtProrrogacao) {
                $this->obTARRDesonerado->setDado( "data_prorrogacao", $this->dtProrrogacao );
            } else {
                $this->obTARRDesonerado->setDado( "data_prorrogacao", null );
            }
            $this->obTARRDesonerado->setDado( "data_revogacao"  , $this->dtRevogacao );
            $obErro = $this->obTARRDesonerado->alteracao( $boTransacao );
            //$this->obTARRDesonerado->debug();exit;
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRDesonerado );

        return $obErro;
    }

    /**
        * Conceder desoneração
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function concederDesoneracao($boTransacao = "", $arCalculo = null)
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRDesonerado->setDado( "cod_desoneracao" , $this->inCodigo );
            $this->obTARRDesonerado->setDado( "numcgm"          , $this->obRCGM->getNumCGM() );
            $this->obTARRDesonerado->setDado( "data_concessao"  , date('d/m/Y') );
            $this->obTARRDesonerado->setDado( "data_prorrogacao", null );
            $this->obTARRDesonerado->setDado( "data_revogacao"  , null );
            $obErro = $this->consultaOcorrenciaDesonerado( $rsDesonerado, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $rsDesonerado->eof() ) {
                    $inOcorrencia = 1;
                } else {
                    $inOcorrencia = $rsDesonerado->getCampo('ocorrencia') + 1;
                }
                $this->obTARRDesonerado->setDado( "ocorrencia" , $inOcorrencia );
                $obErro = $this->obTARRDesonerado->inclusao( $boTransacao );
            }
            $this->inOcorrencia = $inOcorrencia;

            if ( !$obErro->ocorreu() ) {
                if ( $this->getInscricaoImovel() ) {
                    $obTARRDesoneradoImovel = new TARRDesoneradoImovel;
                    $obTARRDesoneradoImovel->setDado( "cod_desoneracao", $this->inCodigo );
                    $obTARRDesoneradoImovel->setDado( "numcgm",  $this->obRCGM->getNumCGM());
                    $obTARRDesoneradoImovel->setDado( "inscricao_municipal", $this->getInscricaoImovel() );
                    $obTARRDesoneradoImovel->setDado( "ocorrencia" , $inOcorrencia );
                    $obErro = $obTARRDesoneradoImovel->inclusao( $boTransacao );
                }else
                if ( $this->getInscricaoEconomica() ) {
                    $obTARRDesoneradoCadEconomico = new TARRDesoneradoCadEconomico;
                    $obTARRDesoneradoCadEconomico->setDado( "cod_desoneracao", $this->inCodigo );
                    $obTARRDesoneradoCadEconomico->setDado( "numcgm",  $this->obRCGM->getNumCGM());
                    $obTARRDesoneradoCadEconomico->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
                    $obTARRDesoneradoCadEconomico->setDado( "ocorrencia" , $inOcorrencia );
                    $obErro = $obTARRDesoneradoCadEconomico->inclusao( $boTransacao );
                }
            }

            if ( !$obErro->ocorreu() ) {
                $inCount = count($arCalculo);
                for ($i=0; $i<$inCount; $i++) {
                    $this->obTARRLancamentoConcedeDesoneracao->setDado( "cod_desoneracao" , $this->inCodigo );
                    $this->obTARRLancamentoConcedeDesoneracao->setDado( "numcgm"          , $this->obRCGM->getNumCGM() );
                    $this->obTARRLancamentoConcedeDesoneracao->setDado( "cod_lancamento"  , $this->inCodLancamento );
                    $this->obTARRLancamentoConcedeDesoneracao->setDado( "cod_calculo"     , $arCalculo[$i]['cod_calculo'] );
                    $this->obTARRLancamentoConcedeDesoneracao->setDado( "ocorrencia"      , $inOcorrencia );
                    $obErro = $this->obTARRLancamentoConcedeDesoneracao->inclusao( $boTransacao );
                }
            }

            if ( !$obErro->ocorreu() ) {
                $arChaveAtributo =  array( "cod_desoneracao" => $this->inCodigo,
                                           "numcgm"          => $this->obRCGM->getNumCGM(),
                                           "ocorrencia"      => $inOcorrencia );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRDesonerado );

        return $obErro;
    }

    /**
        * Definir Desoneracao
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function definirDesoneracao($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTARRDesoneracao->proximoCod( $this->inCodigo , $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obRMONCredito->consultarCredito( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTARRDesoneracao->setDado( "cod_desoneracao"      , $this->inCodigo );
                    $this->obTARRDesoneracao->setDado( "cod_credito"          , $this->obRMONCredito->getCodCredito()  );
                    $this->obTARRDesoneracao->setDado( "cod_especie"          , $this->obRMONCredito->getCodEspecie()  );
                    $this->obTARRDesoneracao->setDado( "cod_genero"           , $this->obRMONCredito->getCodGenero()   );
                    $this->obTARRDesoneracao->setDado( "cod_natureza"         , $this->obRMONCredito->getCodNatureza() );
                    $this->obTARRDesoneracao->setDado( "cod_tipo_desoneracao" , $this->inCodigoTipo );
                    $this->obTARRDesoneracao->setDado( "inicio"               , $this->dtInicio );
                    $this->obTARRDesoneracao->setDado( "termino"              , $this->dtTermino );
                    $this->obTARRDesoneracao->setDado( "expiracao"            , $this->dtExpiracao );
                    $this->obTARRDesoneracao->setDado( "prorrogavel"          , $this->boProrrogavel );
                    $this->obTARRDesoneracao->setDado( "revogavel"            , $this->boRevogavel );
                    $this->obTARRDesoneracao->setDado( "cod_modulo"           , ltrim ( $this->obRMONIndicadorEconomico->getCodModulo(), '0') );
                    $this->obTARRDesoneracao->setDado( "cod_biblioteca"       , ltrim ( $this->obRMONIndicadorEconomico->getCodBiblioteca(), '0') );
                    $this->obTARRDesoneracao->setDado( "cod_funcao"           , ltrim ( $this->obRMONIndicadorEconomico->getCodFuncao(), '0' ));
                    $this->obTARRDesoneracao->setDado( "fundamentacao_legal"  , $this->roUltimaNorma->getCodNorma() );
                    $obErro = $this->obTARRDesoneracao->inclusao( $boTransacao );
                    //$this->obTARRDesoneracao->debug();

                    if ( !$obErro->ocorreu() ) {
                        $arChaveAtributo =  array( "cod_desoneracao" => $this->inCodigo );
                        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                        $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRDesoneracao );

        return $obErro;
    }

    /**
        * Alterar Desoneracao
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function alterarDesoneracao($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obRMONCredito->consultarCredito( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTARRDesoneracao->setDado( "cod_desoneracao"      , $this->getCodigo() );
                $this->obTARRDesoneracao->setDado( "cod_credito"          , $this->obRMONCredito->getCodCredito()  );
                $this->obTARRDesoneracao->setDado( "cod_especie"          , $this->obRMONCredito->getCodEspecie()  );
                $this->obTARRDesoneracao->setDado( "cod_genero"           , $this->obRMONCredito->getCodGenero()   );
                $this->obTARRDesoneracao->setDado( "cod_natureza"         , $this->obRMONCredito->getCodNatureza() );
                $this->obTARRDesoneracao->setDado( "cod_tipo_desoneracao" , $this->inCodigoTipo );
                $this->obTARRDesoneracao->setDado( "inicio"               , $this->dtInicio );
                $this->obTARRDesoneracao->setDado( "termino"              , $this->dtTermino );
                $this->obTARRDesoneracao->setDado( "expiracao"            , $this->dtExpiracao );
                $this->obTARRDesoneracao->setDado( "prorrogavel"          , $this->boProrrogavel );
                $this->obTARRDesoneracao->setDado( "revogavel"            , $this->boRevogavel );

                $this->obTARRDesoneracao->setDado( "cod_modulo"           , ltrim ( $this->obRMONIndicadorEconomico->getCodModulo(), '0') );
                $this->obTARRDesoneracao->setDado( "cod_biblioteca"       , ltrim ( $this->obRMONIndicadorEconomico->getCodBiblioteca(), '0') );
                $this->obTARRDesoneracao->setDado( "cod_funcao"           , ltrim ( $this->obRMONIndicadorEconomico->getCodFuncao(), '0' ));

                $this->obTARRDesoneracao->setDado( "fundamentacao_legal"  , $this->roUltimaNorma->getCodNorma() );
                $obErro = $this->obTARRDesoneracao->alteracao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $arChaveAtributo =  array( "cod_desoneracao" => $this->inCodigo );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                    $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRDesoneracao );

        return $obErro;
    }

    /**
        * Excluir Desoneracao
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function excluirDesoneracao($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $rsRecordSet = new RecordSet();
            $stFiltro = " WHERE cod_desoneracao = ".$this->getCodigo();
            $obErro = $this->obTARRDesonerado->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );

            if (!($rsRecordSet->eof())) {
                $obErro->setDescricao("Desoneração ".$this->getCodigo()." está sendo utilizada pelo sistema. Exclusão não permitida!");

                return $obErro;
            }

            $arChaveAtributo =  array( "cod_desoneracao" => $this->inCodigo );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
            $obErro = $this->obRCadastroDinamico->excluir( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTARRDesoneracao->setDado( "cod_desoneracao" , $this->getCodigo() );
                $obErro = $this->obTARRDesoneracao->exclusao( $boTransacao );
            }

        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRDesoneracao );

        return $obErro;
    }
    /**
        * Verifica se lancamento tem desoneração concedida
        * @access Public
    */
    public function verificaConcessaoDesoneracaoLancamento(&$rsRecordSet, $boTransacao = "" , $inInscricaoMunicipal , $nuValor)
    {
        if ( $this->inCodLancamento && $this->obRMONCredito->getCodCredito() ) {
            $stFiltro  = " and lancamento_concede_desoneracao.cod_lancamento = ".$this->inCodLancamento."\n" ;
            $stFiltro .= " and desonerado_imovel.inscricao_municipal = ".$inInscricaoMunicipal;
            $stFiltro .= " and calculo.cod_credito = ".$this->obRMONCredito->getCodCredito();
            $stFiltro .= " and calculo.cod_especie = ".$this->obRMONCredito->getCodEspecie();
            $stFiltro .= " and calculo.cod_genero  = ".$this->obRMONCredito->getCodGenero();
            $stFiltro .= " and calculo.cod_natureza= ".$this->obRMONCredito->getCodNatureza();

            $obErro = $this->obTARRDesonerado->recuperaLancamentoDesonerado( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao , $nuValor );

            return $obErro;
        }

        return null;
    }

    public function verificaConcessaoDesoneracaoLancamentoCadEco(&$rsRecordSet, $boTransacao = "" , $inInscricaoEconomica , $nuValor)
    {
        if ( $this->inCodLancamento && $this->obRMONCredito->getCodCredito() ) {
            $stFiltro  = " and lancamento_concede_desoneracao.cod_lancamento = ".$this->inCodLancamento."\n" ;
            $stFiltro .= " and desonerado_cad_economico.inscricao_economica = ".$inInscricaoEconomica;
            $stFiltro .= " and calculo.cod_credito = ".$this->obRMONCredito->getCodCredito();
            $stFiltro .= " and calculo.cod_especie = ".$this->obRMONCredito->getCodEspecie();
            $stFiltro .= " and calculo.cod_genero  = ".$this->obRMONCredito->getCodGenero();
            $stFiltro .= " and calculo.cod_natureza= ".$this->obRMONCredito->getCodNatureza();

            $obErro = $this->obTARRDesonerado->recuperaLancamentoDesoneradoCadEco( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao , $nuValor );

            return $obErro;
        }

        return null;
    }

    public function buscarAtributosDisponiveis(& $rsAtributos, $boTransacao = "")
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $stSql = " select cod_atributo, nom_atributo from administracao.atributo_dinamico where cod_modulo = 25 and cod_cadastro = 3 ";
        $obErro = $obConexao->executaSql($rsAtributos, $stSql, $boTransacao);

        return $obErro;
    }

} // fim da classe
