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
  * Página de Regra para Lancamento Transferencia
  * Data de criação : 09/10/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @subpackage Regras
  * @package URBEM

    * $Id: RARRLancamentoTransferencia.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.21
**/

/*
$Log$
Revision 1.2  2007/01/31 17:42:52  cercato
correcao do bug (dt_vencimento eh nulo).

Revision 1.1  2006/10/10 15:17:25  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php"              );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRAtributoITBIValor.class.php"         );
include_once (CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                );
include_once (CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                       );
include_once (CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"                         );
include_once (CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"                      );

class RARRLancamentoTransferencia
{
    /**
        * @access Private
        * @param String
    */
    public $stExercicio;

    /**
        * @access Private
        * @param Object
    */
    public $obRCadastroDinamico;
    public $obTransacao;
    public $obTARRImovelVVenal;
    public $obCalculo;

    /**
        * @access Private
        * @param Float
    */
    public $flValorFinanciado;
    public $flAliquotaValorAvaliado;
    public $flAliquotaValorFinanciado;
    public $flVenalTerritorialInformado;
    public $flVenalPredialInformado;
    public $flVenalTotalInformado;
    public $flVenalTerritorialCalculado;
    public $flVenalPredialCalculado;
    public $flVenalTotalCalculado;
    public $flVenalTerritorialDeclarado;
    public $flVenalPredialDeclarado;
    public $flVenalTotalDeclarado;
    public $flVenalTerritorialAvaliado;
    public $flVenalPredialAvaliado;
    public $flVenalTotalAvaliado;
    public $flValorVenalTerritorial;
    public $flValorVenalPredial;
    public $flValorVenalTotal;

    /**
        * @access Private
        * @param Integer
    */
    public $inNumCGM;

    /**
        * @access Public
        * @param Integer Valor
    */
    public function setInNumCGM($valor) { $this->inNumCGM = $valor; }

    /**
        * @access Public
        * @param Float Valor
    */
    public function setflValorFinanciado($valor) { $this->flValorFinanciado = $valor; }
    public function setflAliquotaValorAvaliado($valor) { $this->flAliquotaValorAvaliado = $valor; }
    public function setflAliquotaValorFinanciado($valor) { $this->flAliquotaValorFinanciado = $valor; }
    public function setflVenalTerritorialInformado($valor) { $this->flVenalTerritorialInformado = $valor; }
    public function setflVenalTotalInformado($valor) { $this->flVenalTotalInformado = $valor; }
    public function setflVenalPredialInformado($valor) { $this->flVenalPredialInformado = $valor; }
    public function setflVenalTerritorialCalculado($valor) { $this->flVenalTerritorialCalculado = $valor; }
    public function setflVenalPredialCalculado($valor) { $this->flVenalPredialCalculado = $valor; }
    public function setflVenalTotalCalculado($valor) { $this->flVenalTotalCalculado = $valor; }
    public function setflVenalTerritorialDeclarado($valor) { $this->flVenalTerritorialDeclarado = $valor; }
    public function setflVenalPredialDeclarado($valor) { $this->flVenalPredialDeclarado = $valor; }
    public function setflVenalTotalDeclarado($valor) { $this->flVenalTotalDeclarado = $valor; }
    public function setflVenalTerritorialAvaliado($valor) { $this->flVenalTerritorialAvaliado = $valor; }
    public function setflVenalPredialAvaliado($valor) { $this->flVenalPredialAvaliado = $valor; }
    public function setflVenalTotalAvaliado($valor) { $this->flVenalTotalAvaliado = $valor; }

    /**
        * @access Public
        * @param String Valor
    */
    public function setExercicio($valor) { $this->stExercicio = $valor; }

    /**
        * @access Public
        * @return Integer
    */
    public function getInNumCGM() { return $this->inNumCGM; }

    /**
        * @access Public
        * @return String Valor
    */
    public function getExercicio() { return $this->stExercicio;  }

    /**
        * Metodo Construtor
        * @access Public
    */
    public function RARRLancamentoTransferencia()
    {
        $this->obTransacao         = new Transacao;
        $this->obTARRImovelVVenal  = new TARRImovelVVenal;
        $this->obRCIMImovel        = new RCIMImovel( new RCIMLote );

        $this->obRCadastroDinamico = new RCadastroDinamico;
        $this->obRCadastroDinamico->setPersistenteValores  ( new TARRAtributoITBIValor );
        $this->obRCadastroDinamico->setCodCadastro         ( 6 );
        $this->obRCadastroDinamico->obRModulo->setCodModulo ( 25 );
    }

    /**
        * Avaliar Imóvel
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function lancarImovel($boTransacao = "")
    {
        $arAdquirentes = $this->getInNumCGM();
        $boFlagTransacao = false;

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRImovelVVenal->setDado( "inscricao_municipal", $this->obRCIMImovel->getNumeroInscricao() );

            $this->obTARRImovelVVenal->setDado( "venal_territorial_avaliado", $this->flVenalTerritorialAvaliado );
            $this->obTARRImovelVVenal->setDado( "venal_predial_avaliado", $this->flVenalPredialAvaliado );
            $this->obTARRImovelVVenal->setDado( "venal_total_avaliado", $this->flVenalTotalAvaliado );

            $this->obTARRImovelVVenal->setDado( "venal_territorial_declarado", $this->flVenalTerritorialDeclarado );
            $this->obTARRImovelVVenal->setDado( "venal_predial_declarado", $this->flVenalPredialDeclarado );
            $this->obTARRImovelVVenal->setDado( "venal_total_declarado", $this->flVenalTotalDeclarado );

            $this->obTARRImovelVVenal->setDado( "venal_territorial_informado", $this->flVenalTerritorialInformado );
            $this->obTARRImovelVVenal->setDado( "venal_predial_informado", $this->flVenalPredialInformado );
            $this->obTARRImovelVVenal->setDado( "venal_total_informado", $this->flVenalTotalInformado );

            $this->obTARRImovelVVenal->setDado( "venal_territorial_calculado", $this->flVenalTerritorialCalculado );
            $this->obTARRImovelVVenal->setDado( "venal_predial_calculado", $this->flVenalPredialCalculado );
            $this->obTARRImovelVVenal->setDado( "venal_total_calculado", $this->flVenalTotalCalculado );

            $this->obTARRImovelVVenal->setDado( "valor_financiado", $this->flValorFinanciado );
            $this->obTARRImovelVVenal->setDado( "aliquota_valor_avaliado", $this->flAliquotaValorAvaliado );
            $this->obTARRImovelVVenal->setDado( "aliquota_valor_financiado", $this->flAliquotaValorFinanciado );

            $this->obTARRImovelVVenal->setDado( "exercicio", $this->getExercicio() );

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
            $inExercicioGrupoITBI = trim($arTMP[1])*1;

            /* montar array de creditos*/
            require_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
            $this->obCalculo->obRARRGrupo = new RARRGrupo;
            $this->obCalculo->obRARRGrupo->setCodGrupo( $inCodGrupoITBI );
            $this->obCalculo->obRARRGrupo->setExercicio( $inExercicioGrupoITBI );
            $this->obCalculo->obRARRGrupo->consultarGrupo($boTransacao);
            $arCalculoImovel = array();
            $obErro = $this->obCalculo->obRARRGrupo->listarCreditosFuncao($rsCreditos, $boTransacao);

            if ( $obErro->ocorreu() ) {
                exit( $obErro->getDescricao() );
            } else {
                if ($rsCreditos->eof()) {
                    $obErro->setDescricao('Não existem créditos cadastrados para o grupo '.$inCodGrupoITBI.'/'.$inExercicioGrupoITBI.'! Verifique se a função para o crédito ITBI está definida.');

                    return $obErro;
                }
            }

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
                    // echo "Funcao: ".$obFuncao->stFuncao."<br>";
                    // echo "valor: ".$nuRetorno."<br>";
                    if (!$obErro->ocorreu() ) {
                        $obErro = $obTARRCalculo->proximoCod($inCodCalculo,$boTransacao);
                        if (!$obErro->ocorreu() ) {
                            $obTARRCalculo->setDado("cod_calculo"     , $inCodCalculo );
                            $obTARRCalculo->setDado("cod_credito"     , $rsCreditos->getCampo("cod_credito")    );
                            $obTARRCalculo->setDado("cod_especie"     , $rsCreditos->getCampo("cod_especie")    );
                            $obTARRCalculo->setDado("cod_genero"      , $rsCreditos->getCampo("cod_genero")     );
                            $obTARRCalculo->setDado("cod_natureza"    , $rsCreditos->getCampo("cod_natureza")   );

                            if (is_array($arAdquirentes) == true) {
                                $obTARRCalculo->setDado("numcgm"          , $arAdquirentes[0]['codigo']         );
                            } else {
                                $obTARRCalculo->setDado("numcgm"          , $this->getInNumCGM()                );
                            }
                            $obTARRCalculo->setDado("exercicio"       , $this->getExercicio());
                            $obTARRCalculo->setDado("valor"           , $nuRetorno);
                            $obTARRCalculo->setDado("nro_parcelas"    , 0                                       );
                            $obTARRCalculo->setDado("calculado"       , true );
                            $obTARRCalculo->setDado("ativo"           , true );
                            $obErro = $obTARRCalculo->inclusao( $boTransacao );
                            if (!$obErro->ocorreu() ) {
                                require_once ( CAM_GT_ARR_MAPEAMENTO."TARRImovelCalculo.class.php"    );
                                $obTARRImovelCalculo = new TARRImovelCalculo;
                                $obTARRImovelCalculo->setDado('cod_calculo'         , $inCodCalculo         );
                                $obTARRImovelCalculo->setDado('inscricao_municipal' , $inInscricaoMunicipal );
                                $obErro = $obTARRImovelCalculo->inclusao( $boTransacao );
                                if (!$obErro->ocorreu() ) {
                                    require_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php"    );
                                    if (is_array($arAdquirentes) == true) {
                                        foreach ($arAdquirentes as $index => $value) {
                                            $obTARRCalculoCgm = new TARRCalculoCgm;
                                            $obTARRCalculoCgm->setDado ('cod_calculo'   , $inCodCalculo );
                                            $obTARRCalculoCgm->setDado ('numcgm'        , $value['codigo']  );
                                            $obErro = $obTARRCalculoCgm->inclusao( $boTransacao );
                                        }
                                    } else {
                                        $obTARRCalculoCgm = new TARRCalculoCgm;
                                        $obTARRCalculoCgm->setDado ('cod_calculo'   , $inCodCalculo );
                                        $obTARRCalculoCgm->setDado ('numcgm'        , $this->getInNumCGM()  );
                                        $obErro = $obTARRCalculoCgm->inclusao( $boTransacao );
                                    }
                                    if (!$obErro->ocorreu() ) {
                                        require_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoGrupoCredito.class.php"    );
                                        $obTARRCalculoGC = new TARRCalculoGrupoCredito;
                                        $obTARRCalculoGC->setDado ('cod_calculo'   , $inCodCalculo  );
                                        $obTARRCalculoGC->setDado ('cod_grupo'     , $inCodGrupoITBI);
                                        $obTARRCalculoGC->setDado ('ano_exercicio' , $inExercicioGrupoITBI);
                                        $obErro = $obTARRCalculoGC->inclusao( $boTransacao );
                                    }
                                }
                            }
                        }
                        if (is_array($arAdquirentes) == true) {
                            $arCalculoImovel[] = array( "cod_calculo"            => $inCodCalculo         ,
                                                        "inscricao_municipal"    => $inInscricaoMunicipal,
                                                        "numcgm"                 => $arAdquirentes[0]['codigo'],
                                                        "valor"                  => $nuRetorno,
                                                        "cod_credito"            => $rsCreditos->getCampo("cod_credito"),
                                                        "cod_grupo"              => $rsCreditos->getCampo("cod_grupo"),
                                                        "exercicio"              => $rsCreditos->getCampo("exercicio") );
                        } else {
                            $arCalculoImovel[] = array( "cod_calculo"            => $inCodCalculo         ,
                                                        "inscricao_municipal"    => $inInscricaoMunicipal,
                                                        "numcgm"                 => $this->getInNumCGM(),
                                                        "valor"                  => $nuRetorno,
                                                        "cod_credito"            => $rsCreditos->getCampo("cod_credito"),
                                                        "cod_grupo"              => $rsCreditos->getCampo("cod_grupo"),
                                                        "exercicio"              => $rsCreditos->getCampo("exercicio") );
                        }
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
            $obRARRLancamento = new RARRLancamento( $this->obCalculo );
            $arParcelasSessao = Sessao::read( "parcelas" );
            $arParcelasSessao[0]['data_vencimento'] = $_REQUEST['dtVencimento'];
            Sessao::write( "parcelas", $arParcelasSessao );
            $obRARRLancamento->setDataVencimento  ( $_REQUEST['dtVencimento']  );
            $obRARRLancamento->setTotalParcelas   ( 0 );
            $obErro = $obRARRLancamento->efetuarLancamentoParcialIndividual($boTransacao,$arCalculoImovel, false);
            $this->obCalculo->obRARRLancamento->inCodLancamento = $obRARRLancamento->inCodLancamento;
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRImovelVVenal );

        return $obErro;
    }
}
