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
* Classe de regra de negócio para Pessoal - Rescindir Contrato
* Data de Criação: 17/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @package URBEM
* @subpackage Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $

* Casos de uso: uc-04.04.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausaNorma.class.php"            );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php"                 );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaCasoCausa.class.php"              );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaCasoCausaNorma.class.php"         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php"                       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"                             );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContrato.class.php"                                     );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                     );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCausaRescisao.class.php"                                );
//include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"              );

/**
    * Classe de Regra de Negócio Pessoal Rescisão Contrato
    * Data de Criação   : 20/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Eduardo Antunez

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalRescisaoContrato
{
    /**
    * @var Object
    * @access Private
    */
    public $obTransacao;
    /**
    * @var Object
    * @access Private
    */
    public $obTPessoalContratoServidorCasoCausa;
    /**
    * @var Object
    * @access Private
    */
    public $obTPessoalContratoPensionista;
    /**
    * @var Object
    * @access Private
    */

    public $obRNorma;
    /**
    * @access Private
    * @var Object
    */

    public $obRPessoalContratoServidor;
    /**
    * @var Object
    * @access Private
    */
    public $obRPessoalCausaRescisao;
    /**
    * @var String
    * @access Private
    */
    public $dtInicial;
    /**
    * @var String
    * @access Private
    */
    public $dtRescisao;
    /**
    * @var Integer
    * @access Private
    */
    public $inExercicio;
    /**
    * @var String
    * @access Private
    */
    public $stAvisoPrevio;
    /**
    * @var Date
    * @access Private
    */
    public $dtAvisoPrevio;
    /**
    * @var String
    * @access Private
    */
    public $stNroCertidaoObito;

    /**
    * @var String
    * @access Private
    */
    public $stDescCausaMortis;
    /**
    * @var Boolean
    * @access Private
    */
    public $boIncorporarFolhaSalario;
    /**
    * @var Boolean
    * @access Private
    */
    public $boIncorporarFolhaDecimo;
    /**
     * @access Public
     * @param Object $valor
     */

    /*var $inCodNorma;
    /**
     * @param Public
     * @param Object $valor
     */

    public function setTransacao(&$valor) { $this->obTransacao  = $valor; }
    /**
     * @access Public
     * @param Object $valor
     */
    public function setTPessoalContratoServidorCasocausa(&$valor) { $this->obTPessoalContratoServidorCasoCausa     = $valor; }
    /**
     * @access  Public
     * @param Object $valor
    */
    public function setTPessoalContratoServidorCasoCausaNorma(&$valor) {$this->obTPessoalContratoServidorCasoCausaNorma  = $valor;}
     /**
     * @access Public
     * @param Object $valor
     */
    public function setRPessoalContratoServidor(&$valor) { $this->obRPessoalContratoServidor              = $valor; }
     /**
     * @access Public
     * @param Object $valor
     */
    public function setRNorma(&$valor) {$this->obRNorma = $valor;}
    /**
     * @access Public
     * @param Object $valor
     */
    public function setRPessoalCausaRescisao(&$valor) { $this->obRPessoalCausaRescisao                 = $valor; }
    /**
     * @access Public
     * @param Object $valor
     */
    public function setDtInicial($valor) { $this->dtInicial                               = $valor; }
    /**
     * @access Public
     * @param Object $valor
     */
    public function setDtRescisao($valor) { $this->dtRescisao                              = $valor; }
    /**
     * @access Public
     * @param Object $valor
     */
    public function setExercicio($valor) { $this->inExercicio                             = $valor; }
    /**
     * @access String
     * @param Object $valor
     */
    public function setAvisoPrevio($valor) { $this->stAvisoPrevio                           = $valor; }
    /**
     * @access Date
     * @param Object $valor
     */
    public function setDataAvisoPrevio($valor) { $this->dtAvisoPrevio                           = $valor; }
    /**
     * @access Public
     * @param Object $valor
     */
    public function setNroCertidaoObito($valor) { $this->stNroCertidaoObito               = $valor; }
    /**
     * @access Public
     * @param Object $valor
     */
    public function setDescCausaMortis($valor) { $this->stDescCausaMortis               = $valor; }
    /**
     * @access Public
     * @param Object $valor
     */
    public function setIncorporarFolhaSalario($valor) { $this->boIncorporarFolhaSalario               = $valor; }
    /**
     * @access Public
     * @param Object $valor
     */
    public function setIncorporarFolhaDecimo($valor) { $this->boIncorporarFolhaDecimo               = $valor; }

    /**
     * @access Public
     * @return Object
     */
    public function getTransacao() { return $this->obTransacao;                               }
    /**
     * @access Public
     * @return Object
     */

    public function getTPessoalContratoServidorCasoCausa() { return $this->obTPessoalContratoServidorCasoCausa;       }
    /**
      * @access Public
      * @return Object
      */
    public function getTPessoalContratoServidorCasoCausaNorma() {return $this->obTPessoalContratoServidorCasoCausaNorma;   }
    /**
     *@access  Public
     * @return Object
     */
    public function getRPessoalContratoServidor() { return $this->obRPessoalContratoServidor;                }
    /**
     *@access  Public
     * @return Object
     */
    public function getRNorma() {return $this->obRNorma; }
    /**
      * @access Public
      * @return Object
      */
    public function getRPessoalCausaRescisao() { return $this->obRPessoalCausaRescisao;                   }
    /**
      * @access Public
      * @return String
      */
    public function getDtInicial() { return $this->dtInicial;                                 }
    /**
      * @access Public
      * @return String
      */
    public function getDtRescisao() { return $this->dtRescisao;                                }
    /**
      * @access Public
      * @return String
      */
    public function getExercicio() { return $this->inExercicio;                               }
    /**
      * @access Public
      * @return String
      */
    public function getAvisoPrevio() { return $this->stAvisoPrevio;                                }
    /**
      * @access Public
      * @return Date
      */
    public function getDataAvisoPrevio() { return $this->dtAvisoPrevio;                                }
    /**
      * @access Public
      * @return String
      */
    public function getNroCertidaoObito() { return $this->stNroCertidaoObito;                                }
    /**
      * @access Public
      * @return String
      */
    public function getDescCausaMortis() { return $this->stDescCausaMortis;                                }
    /**
      * @access Public
      * @return Boolean
      */
    public function getIncorporarFolhaSalario() { return $this->boIncorporarFolhaSalario;                                }
    /**
      * @access Public
      * @return Boolean
      */
    public function getIncorporarFolhaDecimo() { return $this->boIncorporarFolhaDecimo;                                }
    /**
     * Método Construtor
     * @access Private
     */

    public function getInCodNorma() {return $this->inCodNorma;}
    /**
     *@access  Public
     * @return Integer
     */

    public function RPessoalRescisaoContrato()
    {
        $this->setTPessoalContratoServidorCasoCausa        ( new TPessoalContratoServidorCasoCausa                );
        $this->setRPessoalContratoServidor                 ( new RPessoalContratoServidor( new RPessoalServidor ) );
        $this->setRPessoalCausaRescisao                    ( new RPessoalCausaRescisao                            );
        $this->setTPessoalContratoServidorCasoCausaNorma   ( new TPessoalContratoServidorCasoCausaNorma           );
        $this->setTransacao                                ( new Transacao                                        );
        $this->obRPessoalContrato                          = new RPessoalContrato();
        $this->obTPessoalContratoPensionista               = new TPessoalContratoPensionista();
        $this->obTPessoalContratoPensionistaCasoCausa      = new TPessoalContratoPensionistaCasoCausa();
        $this->obTPessoalContratoPensionistaCasoCausaNorma = new TPessoalContratoPensionistaCasoCausaNorma();
    }

    /**
     * Inclui rescisão de contrato
     * @access Public
     * @param  Object $boTransacao Parâmetro Transação, caso esta exista
     * @return Object Objeto Erro retorna o valor, validando o método
     **/
    public function incluirRescisaoContrato($boTransacao = "")
    {
        $obErro = new Erro();
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContratoServidorCasoCausa->setDado( 'cod_contrato'   , $this->obRPessoalContratoServidor->getCodContrato() );
            $this->obTPessoalContratoServidorCasoCausa->setDado( 'cod_caso_causa' , $this->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->getCodCasoCausa() );
            $this->obTPessoalContratoServidorCasoCausa->setDado( 'dt_rescisao'    , $this->getDtRescisao() );
            $this->obTPessoalContratoServidorCasoCausa->setDado( 'inc_folha_salario'  , $this->getIncorporarFolhaSalario() );
            $this->obTPessoalContratoServidorCasoCausa->setDado( 'inc_folha_decimo'  , $this->getIncorporarFolhaDecimo() );
            $obErro = $this->obTPessoalContratoServidorCasoCausa->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu()  and $this->getRNorma() != "") {
           $this->obTPessoalContratoServidorCasoCausaNorma->setDado('cod_norma', $this->getRNorma());
           $this->obTPessoalContratoServidorCasoCausaNorma->setDado('cod_contrato', $this->obRPessoalContratoServidor->getCodContrato());
           $obErro = $this->obTPessoalContratoServidorCasoCausaNorma->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() and $this->getNroCertidaoObito() != "" and $this->getDescCausaMortis() != "" ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaObito.class.php");
            $obTPessoalCausaObito = new TPessoalCausaObito();
            $obTPessoalCausaObito->setDado("cod_contrato",$this->obRPessoalContratoServidor->getCodContrato());
            $obTPessoalCausaObito->setDado("num_certidao_obito",$this->getNroCertidaoObito());
            $obTPessoalCausaObito->setDado("causa_mortis",$this->getDescCausaMortis());
            $obErro = $obTPessoalCausaObito->inclusao($boTransacao);
        }
        if ( !$obErro->ocorreu() and $this->getAvisoPrevio() != "" ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAvisoPrevio.class.php");
            $obTPessoalAvisoPrevio = new TPessoalAvisoPrevio;
            $obTPessoalAvisoPrevio->setDado("cod_contrato",$this->obRPessoalContratoServidor->getCodContrato());
            $obTPessoalAvisoPrevio->setDado("aviso_previo",$this->getAvisoPrevio());
            $obTPessoalAvisoPrevio->setDado("dt_aviso",$this->getDataAvisoPrevio());
            $obErro = $obTPessoalAvisoPrevio->inclusao($boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao,"","",$boTransacao);
        }
        if ( !$obErro->ocorreu()) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoGeraRegistroRescisao.class.php");
            $obFFolhaPagamentoGeraRegistroRescisao = new FFolhaPagamentoGeraRegistroRescisao();
            $obFFolhaPagamentoGeraRegistroRescisao->setDado("cod_contrato",$this->obRPessoalContratoServidor->getCodContrato());
            $obFFolhaPagamentoGeraRegistroRescisao->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obFFolhaPagamentoGeraRegistroRescisao->setDado("exercicio_atual",$this->getExercicio());
            $obErro = $obFFolhaPagamentoGeraRegistroRescisao->geraRegistroRescisao($rsRetorno,$boTransacao);
        }
        ###Assentamento###
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
            $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
            $stFiltro  = " AND assentamento.assentamento_automatico = true";
            $stFiltro .= " AND classificacao_assentamento.cod_tipo = 3";
            $stFiltro .= " AND assentamento_assentamento.cod_motivo = 4";
            $stFiltro .= " AND contrato_servidor_previdencia.cod_contrato = ".$this->obRPessoalContratoServidor->getCodContrato();
            $obErro = $obTPessoalAssentamentoAssentamento->recuperaRelacionamento($rsAssentamentoAssentamento,$stFiltro,"",$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php");
            $obTPessoalCausaRescisao = new TPessoalCausaRescisao();
            $stFiltro = " AND cod_caso_causa = ".$this->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->getCodCasoCausa();
            $obErro = $obTPessoalCausaRescisao->recuperaCausaRescisao($rsCausaRescisao,$stFiltro,"",$boTransacao);
            $stObservacao = $rsCausaRescisao->getCampo("descricao");
        }
        if ( !$obErro->ocorreu() and $this->getRNorma() != "") {
            include_once(CAM_GA_NORMAS_MAPEAMENTO."TNormasNorma.class.php");
            $obTNormasNorma = new TNormasNorma();
            $stFiltro = " AND N.cod_norma = ".$this->getRNorma();
            $obErro = $obTNormasNorma->recuperaNormasDecreto($rsNorma,$stFiltro,"",$boTransacao);
            $stObservacao .= ", ".$rsNorma->getCampo("nom_tipo_norma").", ".$rsNorma->getCampo("descricao");
        }
        if ( !$obErro->ocorreu() ) {
            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php" );
            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
            include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamentoRescisao.class.php" );
            $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor;
            $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado;
            $obFPessoalRegistrarEventoPorAssentamentoRescisao = new FPessoalRegistrarEventoPorAssentamentoRescisao;
            while (!$rsAssentamentoAssentamento->eof()) {
                $obErro = $obTPessoalAssentamentoGeradoContratoServidor->proximoCod( $inCodAssentamentoGerado, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_assentamento_gerado" , $inCodAssentamentoGerado );
                    $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_contrato"            , $this->obRPessoalContratoServidor->getCodContrato() );
                    $obErro = $obTPessoalAssentamentoGeradoContratoServidor->inclusao($boTransacao);
                }
                if ( !$obErro->ocorreu() ) {
                    $obTPessoalAssentamentoGerado->setDado( "cod_assentamento_gerado" , $inCodAssentamentoGerado                   );
                    $obTPessoalAssentamentoGerado->setDado( "cod_assentamento"        , $rsAssentamentoAssentamento->getCampo("cod_assentamento") );
                    $obTPessoalAssentamentoGerado->setDado( "periodo_inicial"         , $this->getDtRescisao()                          );
                    $obTPessoalAssentamentoGerado->setDado( "periodo_final"           , $this->getDtRescisao()                            );
                    $obTPessoalAssentamentoGerado->setDado( "automatico"              , true                              );
                    $obTPessoalAssentamentoGerado->setDado( "observacao"              , $stObservacao );
                    $obErro = $obTPessoalAssentamentoGerado->inclusao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    $obFPessoalRegistrarEventoPorAssentamentoRescisao->setDado("cod_contrato"       ,$this->obRPessoalContratoServidor->getCodContrato());
                    $obFPessoalRegistrarEventoPorAssentamentoRescisao->setDado("cod_assentamento"   ,$rsAssentamentoAssentamento->getCampo("cod_assentamento"));
                    $obErro = $obFPessoalRegistrarEventoPorAssentamentoRescisao->registrarEventoPorAssentamentoRescisao($boTransacao);
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
                $rsAssentamentoAssentamento->proximo();
            }
        }

        return $obErro;
    }

    /**
     * Inclui rescisão de contrato para Pensionista
     * @access Public
     * @param  Object $boTransacao Parâmetro Transação, caso esta exista
     * @return Object Objeto Erro retorna o valor, validando o método
     **/
    public function incluirRescisaoContratoPensionista($boTransacao = "")
    {
        $obErro = new Erro();

        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContratoPensionistaCasoCausa->setDado( 'cod_contrato'   , $this->obRPessoalContrato->getCodContrato() );
            $this->obTPessoalContratoPensionistaCasoCausa->setDado( 'cod_caso_causa' , $this->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->getCodCasoCausa() );
            $this->obTPessoalContratoPensionistaCasoCausa->setDado( 'dt_rescisao'    , $this->getDtRescisao() );
            $this->obTPessoalContratoPensionistaCasoCausa->setDado( 'inc_folha_salario'  , $this->getIncorporarFolhaSalario() );
            $this->obTPessoalContratoPensionistaCasoCausa->setDado( 'inc_folha_decimo'  , $this->getIncorporarFolhaDecimo() );
            $obErro = $this->obTPessoalContratoPensionistaCasoCausa->inclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu()  and $this->getRNorma() != "") {
           $this->obTPessoalContratoPensionistaCasoCausaNorma->setDado('cod_norma', $this->getRNorma());
           $this->obTPessoalContratoPensionistaCasoCausaNorma->setDado('cod_contrato', $this->obRPessoalContrato->getCodContrato());
           $obErro = $this->obTPessoalContratoPensionistaCasoCausaNorma->inclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu() and $this->getNroCertidaoObito() != "" and $this->getDescCausaMortis() != "" ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaObitoPensionista.class.php");
            $obTPessoalCausaObitoPensionista = new TPessoalCausaObitoPensionista();
            $obTPessoalCausaObitoPensionista->setDado("cod_contrato",$this->obRPessoalContrato->getCodContrato());
            $obTPessoalCausaObitoPensionista->setDado("num_certidao_obito",$this->getNroCertidaoObito());
            $obTPessoalCausaObitoPensionista->setDado("causa_mortis",$this->getDescCausaMortis());
            $obErro = $obTPessoalCausaObitoPensionista->inclusao($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao,"","",$boTransacao);
        }

        if ( !$obErro->ocorreu()) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoGeraRegistroRescisao.class.php");
            $obFFolhaPagamentoGeraRegistroRescisao = new FFolhaPagamentoGeraRegistroRescisao();
            $obFFolhaPagamentoGeraRegistroRescisao->setDado("cod_contrato",$this->obRPessoalContrato->getCodContrato());
            $obFFolhaPagamentoGeraRegistroRescisao->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obFFolhaPagamentoGeraRegistroRescisao->setDado("exercicio_atual",$this->getExercicio());
            $obErro = $obFFolhaPagamentoGeraRegistroRescisao->geraRegistroRescisao($rsRetorno,$boTransacao);
        }

        return $obErro;
    }

    /**
     * Exclui rescisão de contrato
     * @access Public
     * @param  Object $boTransacao Parâmetro Transação, caso esta exista
     * @return Object Objeto Erro retorna o valor, validando o método
     **/
    public function excluirRescisaoContrato($boTransacao = "")
    {
        $obErro =  new Erro();
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContratoServidorCasoCausa->setDado('cod_contrato', $this->obRPessoalContratoServidor->getCodContrato() );
            $obErro = $this->obTPessoalContratoServidorCasoCausa->recuperaPorChave($rsContratoRescisao,$boTransacao);
            $arData = explode("/",$rsContratoRescisao->getCampo("dt_rescisao"));
            $dtRescisao = $arData[2]."-".$arData[1]."-".$arData[0];
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAvisoPrevio.class.php");
            $obTPessoalAvisoPrevio = new TPessoalAvisoPrevio;
            $obTPessoalAvisoPrevio->setDado("cod_contrato",$this->obRPessoalContratoServidor->getCodContrato());
            $obErro = $obTPessoalAvisoPrevio->exclusao($boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaObito.class.php");
            $obTPessoalCausaObito = new TPessoalCausaObito();
            $obTPessoalCausaObito->setDado("cod_contrato",$this->obRPessoalContratoServidor->getCodContrato());
            $obErro = $obTPessoalCausaObito->exclusao($boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $obRNorma = new TPessoalContratoServidorCasoCausaNorma();
            $obRNorma->setDado('cod_contrato', $this->obRPessoalContratoServidor->getCodContrato() );
            $obErro = $obRNorma->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContratoServidorCasoCausa->setDado('cod_contrato', $this->obRPessoalContratoServidor->getCodContrato() );
            $obErro = $this->obTPessoalContratoServidorCasoCausa->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoRescisao.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisaoParcela.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculadoDependente.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoRescisao.class.php");
            //recupera último período de movimentação
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao, "", "", $boTransacao);
            $obTFolhaPagamentoRegistroEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao();
            $stFiltro = " AND registro_evento_rescisao.cod_contrato = ".$this->obRPessoalContratoServidor->getCodContrato();
            $obErro = $obTFolhaPagamentoRegistroEventoRescisao->recuperaRelacionamento($rsRegistroRescisao,$stFiltro,"",$boTransacao);
            if ( !$obErro->ocorreu() ) {
                $obTFolhaPagamentoUltimoRegistroEventoRescisao = new TFolhaPagamentoUltimoRegistroEventoRescisao();
                $obTFolhaPagamentoRegistroEventoRescisaoParcela = new TFolhaPagamentoRegistroEventoRescisaoParcela();
                $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();
                $obTFolhaPagamentoEventoRescisaoCalculadoDependente = new TFolhaPagamentoEventoRescisaoCalculadoDependente();
                $obTFolhaPagamentoLogErroCalculoRescisao = new TFolhaPagamentoLogErroCalculoRescisao();
                while ( !$rsRegistroRescisao->eof() ) {
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoRegistroEventoRescisaoParcela->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoEventoRescisaoCalculadoDependente->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoLogErroCalculoRescisao->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    if ($rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao') == $rsRegistroRescisao->getCampo('cod_periodo_movimentacao')) {
                        $obErro = $obTFolhaPagamentoRegistroEventoRescisaoParcela->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoEventoRescisaoCalculadoDependente->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoEventoRescisaoCalculado->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoLogErroCalculoRescisao->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoUltimoRegistroEventoRescisao->exclusao($boTransacao);
                    }
                    if ( $obErro->ocorreu() ) { break; }
                    $rsRegistroRescisao->proximo();
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLancamentoFerias.class.php");
            $obTPessoalLancamentoFerias = new TPessoalLancamentoFerias();
            $obTPessoalFerias = new TPessoalFerias();
            $stFiltro  = " AND ferias.cod_contrato = ".$this->obRPessoalContratoServidor->getCodContrato();
            $stFiltro .= " AND dt_inicio = '".$dtRescisao."'";
            $stFiltro .= " AND dt_fim = '".$dtRescisao."'";
            $stFiltro .= " AND dt_retorno = '".$dtRescisao."'";
            $obErro = $obTPessoalFerias->recuperaRelacionamento($rsFerias,$stFiltro,"",$boTransacao);
            while (!$rsFerias->eof()) {
                $obTPessoalLancamentoFerias->setDado("cod_ferias",$rsFerias->getCampo("cod_ferias"));
                $obErro = $obTPessoalLancamentoFerias->exclusao($boTransacao);
                if ( $obErro->ocorreu() ) { break; }
                $obTPessoalFerias->setDado("cod_ferias",$rsFerias->getCampo("cod_ferias"));
                $obErro = $obTPessoalFerias->exclusao($boTransacao);
                if ( $obErro->ocorreu() ) { break; }
                $rsFerias->proximo();
            }
        }
        ###Assentamento###
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
            $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
            $stFiltro  = " AND assentamento.assentamento_automatico = true";
            $stFiltro .= " AND classificacao_assentamento.cod_tipo = 3";
            $stFiltro .= " AND assentamento_assentamento.cod_motivo = 4";
            $stFiltro .= " AND contrato_servidor_previdencia.cod_contrato = ".$this->obRPessoalContratoServidor->getCodContrato();
            $obErro = $obTPessoalAssentamentoAssentamento->recuperaRelacionamento($rsAssentamentoAssentamento,$stFiltro,"",$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php" );
            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
            include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamento.class.php" );
            $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor;
            $obTPessoalAssentamentoGerado                 = new TPessoalAssentamentoGerado;
            $obFPessoalRegistrarEventoPorAssentamento     = new FPessoalRegistrarEventoPorAssentamento;
            while (!$rsAssentamentoAssentamento->eof()) {
                $stFiltro  = " AND cod_contrato = ".$this->obRPessoalContratoServidor->getCodContrato();
                $stFiltro .= " AND assentamento_gerado.cod_assentamento = ".$rsAssentamentoAssentamento->getCampo("cod_assentamento");
                $obErro = $obTPessoalAssentamentoGerado->recuperaAssentamentoGeradoSemEvento($rsAssentamentoGerado,$stFiltro,"",$boTransacao);
                if ($obErro->ocorreu()) {break;}
                if ($rsAssentamentoGerado->getNumLinhas() == 1) {
                    $obTPessoalAssentamentoGerado->setDado( "cod_assentamento_gerado" , $rsAssentamentoGerado->getCampo("cod_assentamento_gerado")                   );
                    $obErro = $obTPessoalAssentamentoGerado->exclusao( $boTransacao );
                    if ($obErro->ocorreu()) {break;}
                    $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_assentamento_gerado" , $rsAssentamentoGerado->getCampo("cod_assentamento_gerado") );
                    $obErro = $obTPessoalAssentamentoGeradoContratoServidor->exclusao($boTransacao);
                }
                $rsAssentamentoAssentamento->proximo();
            }
        }
        ###Assentamento###
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            $obTPessoalContrato = new TPessoalContrato();
            $stFiltro = " AND contrato.cod_contrato = ".$this->obRPessoalContratoServidor->getCodContrato();
            $obErro = $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro,"",$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao,"","",$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php");
            $obTFolhaPagamentoDeducaoDependente = new TFolhaPagamentoDeducaoDependente();
            $obTFolhaPagamentoDeducaoDependente->setDado("numcgm",$rsCGM->getCampo("numcgm"));
            $obTFolhaPagamentoDeducaoDependente->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoDeducaoDependente->setDado("cod_tipo",5);
            $obErro = $obTFolhaPagamentoDeducaoDependente->exclusao($boTransacao);
        }

        return $obErro;
    }

    /**
     * Exclui rescisão de contrato de pensionista
     * @access Public
     * @param  Object $boTransacao Parâmetro Transação, caso esta exista
     * @return Object Objeto Erro retorna o valor, validando o método
     **/
    public function excluirRescisaoContratoPensionista($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContratoPensionistaCasoCausa->setDado('cod_contrato', $this->obRPessoalContrato->getCodContrato() );
            $obErro = $this->obTPessoalContratoPensionistaCasoCausa->recuperaPorChave($rsContratoRescisao,$boTransacao);
            $arData = explode("/",$rsContratoRescisao->getCampo("dt_rescisao"));
            $dtRescisao = $arData[2]."-".$arData[1]."-".$arData[0];
        }

        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaObitoPensionista.class.php");
            $obTPessoalCausaObitoPensionista = new TPessoalCausaObitoPensionista();
            $obTPessoalCausaObitoPensionista->setDado("cod_contrato",$this->obRPessoalContrato->getCodContrato());
            $obErro = $obTPessoalCausaObitoPensionista->exclusao($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContratoPensionistaCasoCausaNorma->setDado('cod_contrato', $this->obRPessoalContrato->getCodContrato() );
            $obErro = $this->obTPessoalContratoPensionistaCasoCausaNorma->exclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContratoPensionistaCasoCausa->setDado('cod_contrato', $this->obRPessoalContrato->getCodContrato() );
            $obErro = $this->obTPessoalContratoPensionistaCasoCausa->exclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoRescisao.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisaoParcela.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculadoDependente.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoRescisao.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");

            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao, "", "", $boTransacao);
            $obTFolhaPagamentoRegistroEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao();

            $stFiltro = " AND registro_evento_rescisao.cod_contrato = ".$this->obRPessoalContrato->getCodContrato();
            $obErro = $obTFolhaPagamentoRegistroEventoRescisao->recuperaRelacionamento($rsRegistroRescisao,$stFiltro,"",$boTransacao);

            if ( !$obErro->ocorreu() ) {
                $obTFolhaPagamentoUltimoRegistroEventoRescisao = new TFolhaPagamentoUltimoRegistroEventoRescisao();
                $obTFolhaPagamentoRegistroEventoRescisaoParcela = new TFolhaPagamentoRegistroEventoRescisaoParcela();
                $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();
                $obTFolhaPagamentoEventoRescisaoCalculadoDependente = new TFolhaPagamentoEventoRescisaoCalculadoDependente();
                $obTFolhaPagamentoLogErroCalculoRescisao = new TFolhaPagamentoLogErroCalculoRescisao();
                while ( !$rsRegistroRescisao->eof() ) {
                    $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoRegistroEventoRescisaoParcela->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoEventoRescisaoCalculadoDependente->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoLogErroCalculoRescisao->setDado("cod_registro",$rsRegistroRescisao->getCampo("cod_registro"));
                    if ($rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao') == $rsRegistroRescisao->getCampo('cod_periodo_movimentacao')) {
                        $obErro = $obTFolhaPagamentoRegistroEventoRescisaoParcela->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoEventoRescisaoCalculadoDependente->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoEventoRescisaoCalculado->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoLogErroCalculoRescisao->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoUltimoRegistroEventoRescisao->exclusao($boTransacao);
                        if ( $obErro->ocorreu() ) { break; }
                        $obErro = $obTFolhaPagamentoRegistroEventoRescisao->exclusao($boTransacao);
                    }
                    if ( $obErro->ocorreu() ) { break; }
                    $rsRegistroRescisao->proximo();
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            $obTPessoalContrato = new TPessoalContrato();
            $stFiltro = " AND contrato.cod_contrato = ".$this->obRPessoalContrato->getCodContrato();
            $obErro = $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro,"",$boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao,"","",$boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php");
            $obTFolhaPagamentoDeducaoDependente = new TFolhaPagamentoDeducaoDependente();
            $obTFolhaPagamentoDeducaoDependente->setDado("numcgm",$rsCGM->getCampo("numcgm"));
            $obTFolhaPagamentoDeducaoDependente->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoDeducaoDependente->setDado("cod_tipo",5);
            $obErro = $obTFolhaPagamentoDeducaoDependente->exclusao($boTransacao);
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalContratoServidorCasoCausa );

        return $obErro;
    }

    /**
     * Lista contratos de servidor
     * @access Public
     * @param  Object $rsRecordSet Retorna o RecordSet preenchido
     * @param  String $stFiltro    Parâmetro de Filtro
     * @param  String $stOrdem     Parâmetro de Ordenação
     * @param  Object $boTransacao Parâmetro Transação
     * @return Object Objeto Erro
     */
    public function listar(&$rsRecordSet , $stFiltro="" , $stOrdem="" , $boTransacao="")
    {
        $obErro = $this->obTPessoalContratoServidorCasoCausa->recuperaRescisaoContrato($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

        return $obErro;
    }

    /**
     * Lista contratos não rescindidos
     * @access Public
     * @param  Object $rsRecordSet Retorna o RecordSet preenchido
     * @param  Object $boTransacao Parâmetro Transação
     * @return Object Objeto Erro
     */
    public function listarRescisaoContrato(&$rsRecordSet , $boTransacao="")
    {
        if ( $inNumCGM = $this->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->getNumCGM() )
            $stFiltro .= " AND ps.numcgm = ".$inNumCGM;
        if ( $inRegistro = $this->obRPessoalContratoServidor->getRegistro() )
            $stFiltro .= " AND pc.registro = ".$inRegistro;

        return $this->listar($rsRecordSet , $stFiltro , "" , $boTransacao);
    }

     /**
     * Lista contratos rescindidos
     * @access Public
     * @param  Object $rsRecordSet Retorna o RecordSet preenchido
     * @param  Object $boTransacao Parâmetro Transação
     * @return Object Objeto Erro
     */
    public function listarRescisaoContratoRescindidos(&$rsRecordSet , $boTransacao="")
    {
        if ( $inNumCGM = $this->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->getNumCGM() )
            $stFiltro .= " AND ps.numcgm = ".$inNumCGM;
        if ( $inRegistro = $this->obRPessoalContratoServidor->getRegistro() )
            $stFiltro .= " AND pc.registro = ".$inRegistro;
        $this->obTPessoalContratoServidorCasoCausa->setDado('rescindidos',true);

        return $this->listar($rsRecordSet , $stFiltro , "" , $boTransacao);
    }

    /**
     * Consulta o Caso de Causa para rescisão do contrato
     * @access Public
     * @param  Object $rsRecordSet Retorna o RecordSet preenchido
     * @param  String $stFiltro    Parâmetro de Filtro
     * @param  Object $boTransacao Parâmetro Transação
     * @return Object Objeto Erro
     */
    public function consultarCasoCausa(&$rsRecordSet, $stFiltro="", $boTransacao="")
    {
        $this->setDtInicial ( "TO_DATE('". $this->getDtInicial() ."','dd-mm-yyyy')"   );
        $this->setDtRescisao( "TO_DATE('". $this->getDtRescisao()."','dd-mm-yyyy')+1" );
        $this->calculaData($rsCalculaData);

        //Converte a data para meses
        $arTmp = explode(" ",$rsCalculaData->getCampo('tempo'));

        if (substr($arTmp[1],0,4) == "year") {
            $inMeses = $arTmp[0]*12;
            if (substr($arTmp[3],0,3) == "mon") {
                $inMeses += $arTmp[2];
            }
        } elseif (substr($arTmp[1],0,3) == "mon") {
            $inMeses = $arTmp[0];
        } else {
            $inMeses = 0;
        }

        $inMeses = $inMeses < 0 ? $inMeses * -1 : $inMeses;

        $this->obTPessoalContratoServidorCasoCausa->setDado('cod_causa_rescisao' , $this->obRPessoalCausaRescisao->getCodCausaRescisao() );
        $this->obTPessoalContratoServidorCasoCausa->setDado('cod_grupo_periodo'  , $this->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->getCodPeriodo() );
        $this->obTPessoalContratoServidorCasoCausa->setDado('meses'              , $inMeses );
        $this->obTPessoalContratoServidorCasoCausa->setDado('cod_sub_divisao'    , $this->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->roUltimoPessoalSubDivisao->getCodSubDivisao() );
        $obErro = $this->obTPessoalContratoServidorCasoCausa->recuperaCasoCausa($rsRecordSet, $stFiltro);

        return $obErro;
    }

    /**
     * Retorna a diferença em anos e meses entre data inicial e data de rescisao
     * @access Public
     * @param  Object $rsRecordSet Retorna o RecordSet preenchido
     * @param  Object $boTransacao Parâmetro Transação
     * @return Object Objeto Erro
     */
    public function calculaData(&$rsRecordSet,$boTransacao="")
    {
        $obErro = $this->obTPessoalContratoServidorCasoCausa->recuperaCalculaData($rsRecordSet,$this->getDtInicial(),$this->getDtRescisao(),$boTransacao);
    }

    /**
     * Retorna os contratos rescindidos
     * @access Public
     * @param  Object $rsRecordSet Retorna o RecordSet preenchido
     * @param  String $stFiltro    Parâmetro de Filtro
     * @param  Object $boTransacao Parâmetro Transação
     * @return Object Objeto Erro
     */
    public function listarContratosRescindidos(&$rsRecordset , $stFiltro = "", $boTransacao = "")
    {
        if ( $this->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->getNumCGM() )
            $stFiltro .= " AND ps.numcgm = ".$this->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->getNumCGM()." \n";
        $stFiltro .= " AND pc.cod_contrato IN (                                     \n";
        $stFiltro .= "    SELECT                                                    \n";
        $stFiltro .= "        cod_contrato                                          \n";
        $stFiltro .= "    FROM                                                      \n";
        $stFiltro .= "        pessoal.contrato_servidor_caso_causa )                \n";
        $obErro = $this->obRPessoalContratoServidor->roPessoalServidor->obTPessoalServidor->recuperaRegistrosServidor( $rsRecordset, $stFiltro, $boTransacao );
    }

    /**
     * Retorna dados do pensionista a ser rescindido
     * @access Public
     * @param  Object $rsRecordSet Retorna o RecordSet preenchido
     * @return Object Objeto Erro
     */
    public function recuperaDadosRescisaoPensionista($inCodContratoPensionista)
    {
        $obErro = new Erro;
        $obTransacao =  new Transacao;
        $boTransacao = "";

        $stFiltro = "AND registro_pensionista.cod_contrato = ".$inCodContratoPensionista;

        $this->obTPessoalContratoPensionista->recuperaPensionistas($rsPensionista, $stFiltro);
        $arPensionista = $rsPensionista->getElementos();

        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $this->obTPessoalContratoPensionista->setDado('dt_inicio_beneficio', $arPensionista[0]['dt_inicio_beneficio']);

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalContratoPensionista );

        return $obErro;
    }

    /**
     * Caso o sistema esteja em outro periodo de movmentação diferente do periodo de inclusão da rescisão, não poderá alterar o valor da data
     * @access Public
     * @param  Integer    $inCodContratoPensionista Parâmetro de Filtro
     * @return true/false
     */
    public function desabilitarDataRescisaoPensionista($inCodContratoPensionista)
    {
        //verifica se possui rescisão
        $RescisaoContrato = SistemaLegado::pegaDado("dt_rescisao", "pessoal.contrato_pensionista_caso_causa", "WHERE cod_contrato = ".$_REQUEST['inCodContratoPensionista']);

        if (!empty($RescisaoContrato)) {
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao, "", "", $boTransacao);

            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php");
            $obTFolhaPagamentoRegistroEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao();

            $stFiltro = " AND registro_evento_rescisao.cod_contrato = ".$inCodContratoPensionista;
            $obErro = $obTFolhaPagamentoRegistroEventoRescisao->recuperaRelacionamento($rsRegistroRescisao,$stFiltro,"",$boTransacao);

            if ( !$obErro->ocorreu() ) {
                if ($rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao') != $rsRegistroRescisao->getCampo('cod_periodo_movimentacao')) {
                    return true; // para os que possuem rescisao, mas já estão fora do periodo de movimentação.
                } else {
                    return false; // para os que possuem rescisão, mas estão dentro do periodo de movimentação.
                }
            }
        } else {
            return false;// para os que não possuem rescisão
        }
    }

}
