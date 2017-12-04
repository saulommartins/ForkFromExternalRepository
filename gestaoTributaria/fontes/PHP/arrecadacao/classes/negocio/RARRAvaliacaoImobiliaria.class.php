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
  * Página de Regra para Avaliação Imobiliária
  * Data de criação : 13/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @subpackage Regras
  * @package URBEM

    * $Id: RARRAvaliacaoImobiliaria.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.24  2007/03/16 18:51:31  rodrigo
Bug #8425#

Revision 1.23  2007/03/06 12:09:38  dibueno
retirada das linhas em branco ao final do arquivo

Revision 1.22  2006/10/16 11:58:35  cercato
adicionada funcao para retornar valores calculados nao nulos por ordem de timestamp.

Revision 1.21  2006/10/10 15:15:36  cercato
alterando regra de negocio de acordo com modificoes no ER.

Revision 1.20  2006/09/19 16:20:07  domluc
Correção para o Bug #7012

Revision 1.19  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.18  2006/09/15 10:48:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php"              );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRPermissaoValorVenal.class.php"       );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRImovelCalculo.class.php"             );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRAtributoImovelVVenalValor.class.php" );
include_once (CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                );
include_once (CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                       );
include_once (CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"                      );

class RARRAvaliacaoImobiliaria
{
    /**
        * @access Private
        * @param Integer
    */
    public $inNumCGM;

    /**
        * @access Private
        * @param Array
    */
    public $arNumCGM;

    /**
        * @access Private
        * @param Float
    */
    public $flValorFinanciado;

    /**
        * @access Private
        * @param Float
    */
    public $flAliquotaValorAvaliado;

    /**
        * @access Private
        * @param Float
    */
    public $flAliquotaValorFinanciado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalTerritorialInformado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalPredialInformado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalTotalInformado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalTerritorialCalculado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalPredialCalculado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalTotalCalculado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalTerritorialDeclarado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalPredialDeclarado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalTotalDeclarado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalTerritorialAvaliado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalPredialAvaliado;

    /**
        * @access Private
        * @param Float
    */
    public $flVenalTotalAvaliado;

    /**
        * @access Private
        * @param Float
    */
    public $flValorVenalTerritorial;
    /**
        * @access Private
        * @param Float
    */
    public $flValorVenalPredial;
    /**
        * @access Private
        * @param Float
    */
    public $flValorVenalTotal;
    /**
        * @access Private
        * @param Object
    */
    public $obRCadastroDinamico;
    /**
        * @access Private
        * @param Object
    */
    public $obRCIMImovel;
   /**
        * @access Private
        * @param Boolean
    */
    public $boInformado;

    /**
        * @access Private
        * @param Object
    */
    public $obTARRPermissaoValorVenal;
    public $stExercicio ;

    /**
        * @access Private
        * @type Object
    */
    public $obCalculo;

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflValorFinanciado($valor) { $this->flValorFinanciado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflAliquotaValorAvaliado($valor) { $this->flAliquotaValorAvaliado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflAliquotaValorFinanciado($valor) { $this->flAliquotaValorFinanciado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalTerritorialInformado($valor) { $this->flVenalTerritorialInformado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalTotalInformado($valor) { $this->flVenalTotalInformado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalPredialInformado($valor) { $this->flVenalPredialInformado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalTerritorialCalculado($valor) { $this->flVenalTerritorialCalculado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalPredialCalculado($valor) { $this->flVenalPredialCalculado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalTotalCalculado($valor) { $this->flVenalTotalCalculado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalTerritorialDeclarado($valor) { $this->flVenalTerritorialDeclarado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalPredialDeclarado($valor) { $this->flVenalPredialDeclarado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalTotalDeclarado($valor) { $this->flVenalTotalDeclarado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalTerritorialAvaliado($valor) { $this->flVenalTerritorialAvaliado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalPredialAvaliado($valor) { $this->flVenalPredialAvaliado = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflVenalTotalAvaliado($valor) { $this->flVenalTotalAvaliado = $valor; }

    /**
        * @access Public
        * @param Integer Valor
    */
    public function setInNumCGM($valor) { $this->inNumCGM = $valor; }

    /**
        * @access Public
        * @param Array Valor
    */
    public function setArNumCGM($valor) { $this->arNumCGM = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setValorVenalTerritorial($valor) { $this->flValorVenalTerritorial = $valor; }
    /**
        * @access Public
        * @param Float Valor
    */
    public function setValorVenalPredial($valor) { $this->flValorVenalPredial     = $valor; }
    /**
        * @access Public
        * @param Float Valor
    */
    public function setValorVenalTotal($valor) { $this->flValorVenalTotal       = $valor; }
    /**
        * @access Public
        * @param Boolean Valor
    */
    public function setInformado($valor) { $this->boInformado             = $valor; }
    public function setExercicio($valor) { $this->stExercicio             = $valor; }

    /**
        * @access Public
        * @return Integer
    */
    public function getInNumCGM() { return $this->inNumCGM; }

    /**
        * @access Public
        * @return Array
    */
    public function getArNumCGM() { return $this->arNumCGM; }
    /**
        * @access Public
        * @return Float
    */
    public function getValorVenalTerritorial() { return $this->flValorVenalTerritorial; }
    /**
        * @access Public
        * @return Float
    */
    public function getValorVenalPredial() { return $this->flValorVenalPredial;     }
    /**
        * @access Public
        * @return Float
    */
    public function getValorVenalTotal() { return $this->flValorVenalTotal;       }
    /**
        * @access Public
        * @return Boolean
    */
    public function getInformado() { return $this->boInformado;             }
    public function getExercicio() { return $this->stExercicio;             }

    /**
        * Metodo Construtor
        * @access Private
    */
    public function RARRAvaliacaoImobiliaria()
    {
        $this->obTARRPermissaoValorVenal = new TARRPermissaoValorVenal;
        $this->obTransacao         = new Transacao;
        $this->obTARRImovelVVenal  = new TARRImovelVVenal;
        $this->obTARRImovelCalculo = new TARRImovelCalculo;
        $this->obRCIMImovel        = new RCIMImovel( new RCIMLote );
        $this->obRCadastroDinamico = new RCadastroDinamico;
        $this->obRCadastroDinamico->setPersistenteValores  ( new TARRAtributoImovelVVenalValor );
        $this->obRCadastroDinamico->setCodCadastro         ( 4 );
        $this->obRCadastroDinamico->obRModulo->setCodModulo ( 25 );
        unset($this->roCalculo);
    }

    /**
        * Avaliar Imóvel
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function avaliarImovel($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRImovelVVenal->setDado( "inscricao_municipal"     , $this->obRCIMImovel->getNumeroInscricao() );
            $this->obTARRImovelVVenal->setDado( "venal_territorial_informado" , $this->flValorVenalTerritorial );
            $this->obTARRImovelVVenal->setDado( "venal_predial_informado"     , $this->flValorVenalPredial );
            $this->obTARRImovelVVenal->setDado( "venal_total_informado"       , $this->flValorVenalTotal );
            $this->obTARRImovelVVenal->setDado( "informado"               , $this->getInformado() );
            $this->obTARRImovelVVenal->setDado( "exercicio"               , $this->getExercicio() );
            $obErro = $this->obTARRImovelVVenal->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributo =  array( "inscricao_municipal" => $this->obRCIMImovel->getNumeroInscricao() );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            }
        }
        if ( is_object($this->obCalculo) && !$obErro->ocorreu() ) {
            /* consultar na configuração grupo de credito do ITBI  */
            require_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php"           );
            $this->obRARRConfiguracao = new RARRConfiguracao;
            $this->obRARRConfiguracao->setAnoExercicio( Sessao::getExercicio() );
            $this->obRARRConfiguracao->consultar( $boTransacao );
            $arTMP = explode("/",$this->obRARRConfiguracao->getCodigoGrupoCreditoITBI());
            $inCodGrupoITBI = trim($arTMP[0])*1;

            /* montar array de creditos*/
            require_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
            $this->obCalculo->obRARRGrupo = new RARRGrupo;
            $this->obCalculo->obRARRGrupo->setCodGrupo( $inCodGrupoITBI );
            $this->obCalculo->obRARRGrupo->consultarGrupo($boTransacao);
            $arCalculoImovel = array();
            $obErro = $this->obCalculo->obRARRGrupo->listarCreditosFuncao($rsCreditos, $boTransacao);
            if ( $obErro->ocorreu() )
                exit( $obErro->getDescricao() );
            /* incluir objetos de credito */
            while ( !$rsCreditos->eof() ) {
                if ( !$obErro->ocorreu() ) {
                    require_once ( CAM_GT_ARR_MAPEAMENTO.'FCalculo.class.php'       );
                    require_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php"    );
                    $obTARRCalculo = new TARRCalculo;
                    $obFuncao = new FCalculo;

                    $obFuncao->stFuncao = $rsCreditos->getCampo("nom_funcao");
                    $inInscricaoMunicipal = $this->obRCIMImovel->getNumeroInscricao();
                    $stParametros = $inInscricaoMunicipal.",".$this->getExercicio();

                    $obErro = $obFuncao->executaFuncao($rsRetornoFuncao,$stParametros,$boTransacao);
                    $nuRetorno = $rsRetornoFuncao->getCampo("valor");
                    if (!$obErro->ocorreu() ) {
                        $obErro = $obTARRCalculo->proximoCod($inCodCalculo,$boTransacao);
                        if (!$obErro->ocorreu() ) {
                            $obTARRCalculo->setDado("cod_calculo"     , $inCodCalculo );
                            $obTARRCalculo->setDado("cod_credito"     , $rsCreditos->getCampo("cod_credito")    );
                            $obTARRCalculo->setDado("cod_especie"     , $rsCreditos->getCampo("cod_especie")    );
                            $obTARRCalculo->setDado("cod_genero"      , $rsCreditos->getCampo("cod_genero")     );
                            $obTARRCalculo->setDado("cod_natureza"    , $rsCreditos->getCampo("cod_natureza")   );
                            $obTARRCalculo->setDado("numcgm"          , $this->getInNumCGM()                    );
                            $obTARRCalculo->setDado("exercicio"       , $this->getExercicio());
                            $obTARRCalculo->setDado("valor"           , $nuRetorno);
                            $obTARRCalculo->setDado("nro_parcelas"    , 0                                       );
                            $obTARRCalculo->setDado("calculado"       , true );
                            $obErro = $obTARRCalculo->inclusao( $boTransacao );
                            if (!$obErro->ocorreu() ) {
                                require_once ( CAM_GT_ARR_MAPEAMENTO."TARRImovelCalculo.class.php"    );
                                $obTARRImovelCalculo = new TARRImovelCalculo;
                                $obTARRImovelCalculo->setDado('cod_calculo'         , $inCodCalculo         );
                                $obTARRImovelCalculo->setDado('inscricao_municipal' , $inInscricaoMunicipal );
                                $obErro = $obTARRImovelCalculo->inclusao( $boTransacao );
                                if (!$obErro->ocorreu() ) {
                                    require_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php"    );
                                    $obTARRCalculoCgm = new TARRCalculoCgm;
                                    $obTARRCalculoCgm->setDado ('cod_calculo'   , $inCodCalculo );
                                    $obTARRCalculoCgm->setDado ('numcgm'        , $this->getInNumCGM()  );
                                    $obErro = $obTARRCalculoCgm->inclusao( $boTransacao );
                                    if (!$obErro->ocorreu() ) {
                                        require_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoGrupoCredito.class.php"    );
                                        $obTARRCalculoGC = new TARRCalculoGrupoCredito;
                                        $obTARRCalculoGC->setDado ('cod_calculo'   , $inCodCalculo  );
                                        $obTARRCalculoGC->setDado ('cod_grupo'     , $inCodGrupoITBI);
                                        $obTARRCalculoGC->setDado ('ano_exercicio' , $this->getExercicio());
                                        $obErro = $obTARRCalculoGC->inclusao( $boTransacao );
                                    }
                                }
                            }
                        }
                        $arCalculoImovel[] = array( "cod_calculo"            => $inCodCalculo         ,
                                                    "inscricao_municipal"    => $inInscricaoMunicipal,
                                                    "numcgm"                 => $this->getInNumCGM(),
                                                    "valor"                  => $nuRetorno,
                                                    "cod_credito"            => $rsCreditos->getCampo("cod_credito") );
                        $this->obCalculo->obRARRGrupo->addCredito();
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->refGrupoCredito( new RARRGrupo);
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->roRARRGrupo->setCodGrupo    ( $inCodGrupoITBI);
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->roRARRGrupo->setCodModulo   ( 12 );
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->roRARRGrupo->setExercicio   ($rsCreditos->getCampo("exercicio"));
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->setCodCredito           ( $rsCreditos->getCampo("cod_credito")          );
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->setNomeFuncao           ( $rsCreditos->getCampo("nom_funcao")           );
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->setCodEspecie           ( $rsCreditos->getCampo("cod_especie")          );
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->setCodGenero            ( $rsCreditos->getCampo("cod_genero")           );
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->setCodNatureza          ( $rsCreditos->getCampo("cod_natureza")         );
                        $this->obCalculo->obRARRGrupo->roUltimoCredito->setValorCorrespondente  ( $rsCreditos->getCampo("valor_correspondente") );
                        $this->obCalculo->inCodCalculo = $inCodCalculo;
                    }
                }
                $rsCreditos->proximo();
            }
            include_once ( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php"                        );
            $obRARRLancamento = new RARRLancamento(  $this->obCalculo );
            $arParcelas = Sessao::read( "parcelas" );
            $arParcelas[0]['dtVencimento'] = $_REQUEST['dtVencimento'];
            Sessao::write( "parcelas", $arParcelas );
            $obRARRLancamento->setDataVencimento  ( $_REQUEST['dtVencimento']  );
            $obRARRLancamento->setTotalParcelas   ( 0 );
            $obErro = $obRARRLancamento->efetuarLancamentoParcialIndividual($boTransacao,$arCalculoImovel);
            $this->obCalculo->obRARRLancamento->inCodLancamento = $obRARRLancamento->inCodLancamento;
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRImovelVVenal );

        return $obErro;
    }
    /**
        * Recupera uma lista de avaliações de acordo com o filtro
        * @access Public
        * @param RecordSet, Transacao
        * @return RecordSet
    */
    public function listarAvaliacoes(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getExercicio() ) {
            $stFiltro .= " AND iv.exercicio = ".$this->getExercicio();
        }

        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = " AND iv.INSCRICAO_MUNICIPAL = ".$this->obRCIMImovel->getNumeroInscricao();
        }
        $stOrdem = " ORDER BY iv.INSCRICAO_MUNICIPAL ";
        $obErro = $this->obTARRImovelVVenal->recuperaAvaliacaoImoveis( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Recupera uma lista de avaliações INFORMADAS de acordo com o filtro
        * @access Public
        * @param RecordSet, Transacao
        * @return RecordSet
    */
    public function listarAvaliacoesInformada(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getExercicio() ) {
            $stFiltro .= " AND iv.exercicio = ".$this->getExercicio();
        }

        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = " AND iv.INSCRICAO_MUNICIPAL = ".$this->obRCIMImovel->getNumeroInscricao();
        }
        $stOrdem = " ORDER BY iv.timestamp desc, iv.INSCRICAO_MUNICIPAL ";
        $obErro = $this->obTARRImovelVVenal->recuperaAvaliacaoImoveisInformado( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Recupera uma lista de avaliações CALCULADAS de acordo com o filtro
        * @access Public
        * @param RecordSet, Transacao
        * @return RecordSet
    */
    public function listarAvaliacoesCalculada(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->getExercicio() ) {
            $stFiltro .= " AND iv.exercicio = '".$this->getExercicio()."'";
        }

        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro .= " AND iv.INSCRICAO_MUNICIPAL = ".$this->obRCIMImovel->getNumeroInscricao();
        }

        $stOrdem = " ORDER BY iv.timestamp desc,iv.INSCRICAO_MUNICIPAL ";
        $obErro = $this->obTARRImovelVVenal->recuperaAvaliacaoImoveisCalculado( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Consulta de avaliações de acordo com o filtro
        * @access Public
        * @param RecordSet, Transacao
        * @return RecordSet
    */
    public function consultarAvaliacoes($boTransacao = "")
    {
        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $obErro = $this->listarAvaliacoes( $rsRecordSet, $boTransacao );
            if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
                $this->obRCIMImovel->setNumeroInscricao( $rsRecordSet->getCampo( "inscricao_municipal" ));
                $this->setValorVenalTerritorial( $rsRecordSet->getCampo( "valor_venal_territorial" )    );
                $this->setValorVenalPredial    ( $rsRecordSet->getCampo( "valor_venal_predial" )        );
                $this->setValorVenalTotal      ( $rsRecordSet->getCampo( "valor_venal_total" )          );
                $this->setInformado            ( $rsRecordSet->getCampo( "informado" )                  );
            }
        }
    }

    /**
        * Inclui Usuario na lista de usuarios que podem alterar calculo venal
        * @access Public
        * @param Transacao
        * @return Erro
    */
    public function IncluirPermissaoUsuario($boTransacao = "")
    {
        $inTotalCGM = count ( $this->arNumCGM );
        $obErro     = new Erro();
        for ($inCount = 0; $inCount < $inTotalCGM; $inCount++) {
            $this->obTARRPermissaoValorVenal->setDado("numcgm", $this->arNumCGM[$inCount]["inNumCGM"] );
            $obErro = $this->obTARRPermissaoValorVenal->inclusao( $boTransacao );
            if ( $obErro->ocorreu() )
                break;
        }

        return $obErro;
    }

    /**
        * Lista Usuario que podem alterar calculo venal
        * @access Public
        * @param RecordSet, Transacao
        * @return Erro
    */
    public function ListarPermissaoUsuario(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getInNumCGM() ) {
            $stFiltro = " WHERE numcgm = ". $this->getInNumCGM();
        }

        $stOrdem = "";
        $obErro = $this->obTARRPermissaoValorVenal->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarVenaisImoveisConsulta(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = "\t\n WHERE inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao();
        }

        $stOrdem = " order by data desc";
        $obErro = $this->obTARRImovelVVenal->recuperaVenaisImovelConsulta( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Recupera uma lista de avaliações Calculadas de acordo com o filtro
        * @access Public
        * @param RecordSet, Transacao
        * @return RecordSet
    */
    public function listarAvaliacaoCalculadoNaoNulo(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getExercicio() ) {
            $stFiltro .= " AND iv.exercicio = ".$this->getExercicio();
        }

        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = " AND iv.INSCRICAO_MUNICIPAL = ".$this->obRCIMImovel->getNumeroInscricao();
        }

        $stOrdem = " ORDER BY iv.timestamp desc, iv.INSCRICAO_MUNICIPAL ";
        $obErro = $this->obTARRImovelVVenal->recuperaAvaliacaoImoveisCalculadoNaoNulo( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }
}
