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


/**
    * Classe de regra de negócio para arrecadacao calculo
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: RARRCalculo.class.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.03.05
*/

set_time_limit(0);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRCalculo.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRCalculoCgm.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRLogCalculo.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRLogTemp.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRImovelCalculo.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRImovelVVenal.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRImovelCalculo.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRAcrescimoCalculo.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRCalculoGrupoCredito.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "FARRAbreCalculo.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "FARRFechaCalculo.class.php");
include_once (CAM_GT_ARR_FUNCAO . "Fcalculaimpostoterritorial.class.php");
include_once (CAM_GT_ARR_MAPEAMENTO . "TARRCadastroEconomicoCalculo.class.php");
include_once (CAM_GT_ARR_NEGOCIO . "RARRGrupo.class.php");
include_once (CAM_GT_ARR_NEGOCIO . "RARRGrupoVencimento.class.php");
include_once (CAM_GT_ARR_NEGOCIO . "RARRLancamento.class.php");
include_once (CAM_GT_MON_NEGOCIO . "RMONCredito.class.php");
include_once (CAM_GT_MON_NEGOCIO . "RMONConvenio.class.php");
include_once (CAM_GT_MON_NEGOCIO . "RMONAcrescimo.class.php");
include_once (CAM_GT_CIM_NEGOCIO . "RCIMImovel.class.php");
include_once (CAM_GT_CEM_NEGOCIO . "RCEMInscricaoEconomica.class.php");
include_once (CAM_GA_ADM_NEGOCIO . "RModulo.class.php");
include_once (CAM_GA_CGM_NEGOCIO . "RCGM.class.php");
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoria.class.php" );

/**
    * Classe de regra de negócio para arrecadacao calculo
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra
*/

class RARRCalculo
{
    /**
    * @access Private
    * @var Integer
    */
    public $inCodCalculo;
    /**
    * @access Private
    * @var String
    */
    public $stExercicio;
    /**
    * @access Private
    * @var Date
    */
    public $dtDataLancamento;
    /**
    * @access Private
    * @var Float
    */
    public $flValor;
    /**
    * @access Private
    * @var Integer
    */
    public $inNumParcelas;
    /**
    * @access Private
    * @var Date
    */
    public $dtDataBaixa;
    /**
    * @access Private
    * @var Array
    */
    public $arCreditos;
    /**
    * @access Private
    * @var Array
    */
    public $boLancamento;
    public $boTipoCalculo;
    /**
    * @access Private
    * @var Array
    */
    public $stParametros;
    public $inTipoCalculo;
    public $stChaveCredito;

    // SETTERS
    /**
    * @access Public
    * @param Integer $valor
    */
    public function setCodCalculo($valor)
    {
        $this->inCodCalculo = $valor;
    }
    /**
    * @access Public
    * @param Float $valor
    */
    public function setValor($valor)
    {
        $this->flValor = $valor;
    }
    /**
    * @access Public
    * @param Integer $valor
    */
    public function setNumParcelas($valor)
    {
        $this->inNumParcelas = $valor;
    }
    /**
    * @access Public
    * @param Integer $valor
    */
    public function setDataLancamento($valor)
    {
        $this->dtDataLancamento = $valor;
    }
    /**
    * @access Public
    * @param Date $valor
    */
    public function setDataBaixa($valor)
    {
        $this->dtDataBaixa = $valor;
    }
    /**
    * @access Public
    * @param String $valor
    */
    public function setExercicio($valor)
    {
        $this->stExercicio = $valor;
    }
    public function setParametros($valor)
    {
        $this->stParametros = $valor;
    }
    public function setTipoCalculo($valor)
    {
        $this->inTipoCalculo = $valor;
    }
    public function setChaveCredito($valor)
    {
        $this->stChaveCredito = $valor;
    }
    public function setLancamento($valor) { $this->boLancamento = $valor; }

    // GETTERES
    /**
    * @access Public
    * @return Integer
    */
    public function getCodCalculo()
    {
        return $this->inCodCalculo;
    }
    /**
    * @access Public
    * @return Integer
    */
    public function getNumParcelas()
    {
        return $this->inNumParcelas;
    }
    /**
    * @access Public
    * @return Float
    */
    public function getValor()
    {
        return $this->flValor;
    }
    /**
    * @access Public
    * @return Date
    */
    public function getDataLancamento()
    {
        return $this->dtDataLancamento;
    }
    /**
    * @access Public
    * @return Date
    */
    public function getDataBaixa()
    {
        return $this->dtDataBaixa;
    }
    /**
    * @access Public
    * @return String
    */
    public function getExercicio()
    {
        return $this->stExercicio;
    }
    public function getParametros()
    {
        return $this->stParametros;
    }
    public function getTipoCalculo()
    {
        return $this->inTipoCalculo;
    }
    public function getChaveCredito()
    {
        return $this->stChaveCredito;
    }
    public function getLancamento()
    {
        return $this->boLancamento;
    }

    /**
     * Método construtor
     * @access Private
    */
    public function RARRCalculo()
    {
        // mapeamento
        $this->obTARRCalculo = new TARRCalculo;
        $this->obTARRLogCalculo = new TARRLogCalculo;
        $this->obTARRLogTemp = new TARRLogTemp;
        $this->obTARRAcrescimoCalculo = new TARRAcrescimoCalculo;
        $this->obTARRCalculoGrupoCredito = new TARRCalculoGrupoCredito;
        $this->obTARRArrecadacaoModulos = new TARRArrecadacaoModulos;
        $this->obTARRImovelVVenal = new TARRImovelVVenal;
        $this->obTARRImovelCalculo = new TARRImovelCalculo;
        //funcoes
        $this->obFARRAbreCalculo = new FARRAbreCalculo;
        $this->obFARRFechaCalculo = new FARRFechaCalculo;
        // regras
        $this->obRMONAcrescimo = new RMONAcrescimo;
        $this->obRARRGrupo = new RARRGrupo;
        $this->obRARRGrupoVencimento = new RARRGrupoVencimento(new RARRCalendarioFiscal);
        $this->obRMONCredito = new RMONCredito;
        $this->obRMONConvenio = new RMONConvenio;
        $this->obRCIMImovel = new RCIMImovel(new RCIMLote);
        $this->obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
        $this->obRModulo = new RModulo;
        $this->obRCGM = new RCGM;

        //$this->obRARRLancamento        = new RARRLancamento($this);

        //
        $this->obTransacao = new Transacao;
        $this->inNumParcelas = 1;
    }

    /**
    * Função Abstrata de Calculo
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */

    public function calculoTributario($boTransacao = false)
    {
        $obErro = new Erro;
        //$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        /* creditos ou grupo */
        if ( $this->getChaveCredito() ) {
            $this->obRARRGrupo->setCodGrupo(null);
        }else
            if ( $this->obRARRGrupo->getCodGrupo() ) {
                $obTARRCalculo = new TARRCalculo;
                $stFiltro = "";
                switch ( $this->obRModulo->getCodModulo() ) {
                    case 12 :
                        $this->obRCIMImovel->listarImoveisConsulta( $rsImoveis, $boTransacao );
                        if ( !$rsImoveis->Eof() ) {
                            $stFiltro .= " AND aic.inscricao_municipal IN ( ";
                            $stFiltro .= $rsImoveis->getCampo("inscricao_municipal");
                            while ( !$rsImoveis->Eof() ) {
                                $stFiltro .= ", ".$rsImoveis->getCampo("inscricao_municipal");
                                $rsImoveis->proximo();
                            }

                            $stFiltro .= " ) ";
                            unset ( $rsImoveis );
                        }
                        break;

                    case 14 :
                        $this->obRCEMInscricaoEconomica->listarInscricaoConsulta( $rsEmpresas, $boTransacao );
                        if ( !$rsEmpresas->Eof() ) {
                            $stFiltro .= " AND acec.inscricao_economica IN ( ";
                            $stFiltro .= $rsEmpresas->getCampo("inscricao_economica");
                            while ( !$rsEmpresas->Eof() ) {
                                $stFiltro .= ", ".$rsEmpresas->getCampo("inscricao_economica");
                                $rsEmpresas->proximo();
                            }

                            $stFiltro .= " ) ";
                            unset ( $rsEmpresas );
                        }
                        break;
                }

                $obTARRCalculo->listaCalculosSemLancamentoPorGrupo( $rsCalculos, $this->obRARRGrupo->getCodGrupo(), $this->obRARRGrupo->getExercicio(), $stFiltro, $boTransacao );
                $obConexao = new Conexao;
                while ( !$rsCalculos->Eof() ) {
                    $stSql = " UPDATE arrecadacao.calculo SET ativo = false WHERE cod_calculo = ".$rsCalculos->getCampo("cod_calculo");
                    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

                    $rsCalculos->proximo();
                }

                unset( $rsCalculos );
                $this->setChaveCredito(null);
            }

        /* carrega mapeamento da função abstrata de calculo */
        require_once (CAM_GT_ARR_MAPEAMENTO . "FARRCalculoTributario.class.php");

        /* instancia objecto do map */
        $this->obCalculoTributario = new FARRCalculoTributario;
        /* Chamar metodo de acordo com o modulo */
        #echo '<br>CodModulo: '.$this->obRModulo->getCodModulo(); #exit;
        switch ($this->obRModulo->getCodModulo()) {
            case 12 :
                $this->obErro = $this->calculoImobiliario( $boTransacao );
            break;
            case 14 :
                $this->obErro = $this->calculoEconomico( $boTransacao );
            break;
            case 0 :
                ;//$this->calculoCgm($boTransacao);
            break;
        }
        #echo 'e vamos fechar a transação';
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $this->obErro );

        return $this->obErro;
    }

    /**
    * Abstrai rotina para lançamento do calculo
    */
    public function lancamentoTributario($boTransacao , $arCalculos, $boLcEc)
    {
        if (!is_object($obErro))
            $obErro = new Erro;

        if ($this->boLancamento == 'true') {

            $this->obRARRLancamento = new RARRLancamento( $this );
            $this->obRARRLancamento->refCalculo( $this );

            $this->obRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo( $this->obRARRGrupo->getCodGrupo() );
            $this->obRARRLancamento->roRARRCalculo->obRARRGrupo->setExercicio( $this->getExercicio() );
            $this->obRARRLancamento->roRARRCalculo->setExercicio( $this->getExercicio() );

            $obErro = $this->obRARRLancamento->efetuarLancamentoParcialIndividual( $boTransacao, $arCalculos, $boLcEc );

        }

        return $obErro;
    }

    /* calculo do imobiliario */
    public function calculoImobiliario($boTransacao = false)
    {
        if (!is_object($obErro))
            $obErro = new Erro;
        if (!$obErro->ocorreu()): // >> if1
            Sessao::write( 'calculos', '' );
            Sessao::write( 'arCalculoErro', array() );
            $obErro = $this->obRCIMImovel->listarImoveisConsulta( $rsImoveis, $boTransacao);

            #echo 'Numero de Imoveis: '. $rsImoveis->getNumLinhas();
            #exit;
            #if ( !$obErro->ocorreu() ) {
            if (  $rsImoveis->getNumLinhas() > 0 ): // >> if2
                /* loop nos imoveis selecionados */
                $arCalculos = array ();

                $nome_arquivo  = "calculos_".date("Y-m-d").'_'.date("h-m-s");
                $nome_arquivo  = "/tmp/".$nome_arquivo.'.txt';

                $nome_arquivo2  = "lancamentos_".date("Y-m-d").'_'.date("h-m-s");
                $nome_arquivo2  = "/tmp/".$nome_arquivo2.'.txt';
                #echo '<b>'.$nome_arquivo2.'</b>';

                        $boFlagTransacao = true;
    //                      $boTransacao = "";
                $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                    set_time_limit(0);
                    while (!$rsImoveis->eof()) { // WHILE DOS IMOVEIS

                        #echo "<hr>Imovel: ".$rsImoveis->getCampo('inscricao_municipal');
                        #echo "<br>Uso de Memoria INICIAL: <b>".round((memory_get_usage()/1024)/1024)." MB</b>";

                        if ( $rsImoveis->getCampo('situacao') != 'Ativo' ) {
                            $rsImoveis->proximo();
                        } else {

//						    $boFlagTransacao = false;
    //					    $boTransacao = "";
        //				    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            } else {

                                $arquivo = fopen ( $nome_arquivo, "a" );
                                $arquivo2 = fopen ( $nome_arquivo2, "a" );

                                if (!$arquivo) {
                                    $obErro->setDescricao("Não foi possível criar arquivo necessário para realizar cálculo.");
                                } else {

                                $this->obCalculoTributario->setDado('inRegistro', $rsImoveis->getCampo('inscricao_municipal'));
                                $this->obCalculoTributario->setDado('inExercicio', $this->getExercicio());
                                $this->obCalculoTributario->setDado('stGrupo', $this->obRARRGrupo->getCodGrupo());
                                $this->obCalculoTributario->setDado('stCredito', $this->getChaveCredito());
                                $this->obCalculoTributario->setDado('stModulo', 12);
                                $obErro = $this->obCalculoTributario->calculoTributario( $rsCalculo, $boTransacao);

                                // verifica erro na execução da consulta sql
                                if ( $obErro->ocorreu() ) {
                                    break;
                                } else {

                                    #echo 'RsCalculso: '.$rsCalculo->getCampo('retorno').'<br>';
                                    #echo 'NumLinhas: '.$rsCalculo->getNumLinhas().'<br>';
                                    // vericifica erro na execução do calculo
                                    if ( $rsCalculo->getCampo('retorno') == 'f' || $rsCalculo->getNumLinhas() < 1) {

                                        #echo ' <b> [CALCULO ERRO] </b>';

                                        if ( Sessao::read( 'TipoCalculo' ) == "individual" ) {

                                            $this->buscarCalculosMensagem($rsCalculosMensagens, $boTransacao);
                                            Sessao::write('arCalculoMensagens', $rsCalculosMensagens);

                                            $this->buscarCalculosErros($rsCalculosMensagens,$boTransacao);
                                            Sessao::write('arCalculoErro', $rsCalculosMensagens);
                                            if ( $rsCalculosMensagens->getNumLinhas() > 0 ) {
                                                $obErro->setDescricao( $rsCalculosMensagens->getCampo('mensagem') );
                                            } else {
                                                $obErro->setDescricao("Não foi possível realizar Cálculo Individual para a inscrição municipal [<b>".$rsImoveis->getCampo('inscricao_municipal')."</b>]");
                                            }

                                        }

                                        $boErroCalculo = true;

                                    } else {
                                        // listar calculos
                                        $this->buscarCalculos( $rsCalculos, $boTransacao);
                                        #echo ' <b>[BUSCA CALCULOS OK]</b>';
                                        #echo '                           ';
                                        #echo " Memoria:".round((memory_get_usage()/1024)/1024)." MB";
                                        if ( $rsCalculos->getNumLinhas() < 1 ) {
                                            if ( Sessao::read( 'TipoCalculo' ) == "individual" ) {

                                                $this->buscarCalculosMensagem($rsCalculosMensagens, $boTransacao);

                                                if ( $rsCalculosMensagens->getNumLinhas() > 0 ) {
                                                    $obErro->setDescricao( $rsCalculosMensagens->getCampo('mensagem') );
                                                } else {
                                                    $obErro->setDescricao("Nenhum cálculo encontrado para o Imóvel [<b>". $rsImoveis->getCampo('inscricao_municipal') ."</b>] !");
                                                }

                                            }
                                        } else {

                                            // acrescentar imovel a lista de calculos
                                            $arCalculos = array ();
                                            $rsCalculos->setPrimeiroElemento();
                                            $obTAdministracaoAuditoria = new TAuditoria;
                                            $stTimestampAtual = "";
                                            while (!$rsCalculos->eof()) {
                                                if ( $stTimestampAtual == $rsCalculos->getCampo("timestamp") ) {
                                                    $stCalculos .= ",".$rsCalculos->getCampo("cod_calculo");
                                                } else {
                                                    if ($stTimestampAtual) {
                                                        $obTAdministracaoAuditoria->setDado( "numcgm", Sessao::read( "numCgm" ) );
                                                        $obTAdministracaoAuditoria->setDado( "cod_acao", Sessao::read( "acao" ) );
                                                        $obTAdministracaoAuditoria->setDado( "timestamp", $stTimestampAtual );
                                                        $obTAdministracaoAuditoria->setDado( "objeto", $stCalculos );
                                                        $obTAdministracaoAuditoria->setDado( "transacao", false );
                                                        $obTAdministracaoAuditoria->inclusao( $boTransacao );
                                                    }

                                                    $stCalculos = "cod_calculo=".$rsCalculos->getCampo("cod_calculo");
                                                    $stTimestampAtual = $rsCalculos->getCampo("timestamp");
                                                }
                                                // Array de Calculos para Lancamento
                                                if ( $rsCalculos->getCampo('cod_calculo') ) {
                                                    $arCalculos[] = array (
                                                        'cod_calculo' => $rsCalculos->getCampo('cod_calculo')
                                                        , 'valor' => $rsCalculos->getCampo('valor')
                                                        , 'inscricao_municipal' => $rsImoveis->getCampo('inscricao_municipal'));
                                                    $strTMPLanc  = $rsCalculos->getCampo('cod_calculo').'&';
                                                    $strTMPLanc .= $rsCalculos->getCampo('valor').'&';
                                                    $strTMPLanc .= $rsImoveis->getCampo('inscricao_municipal')."\n";

                                                    fwrite( $arquivo2,  $strTMPLanc );

                                                }
                                                $strTMP = $rsCalculos->getCampo('cod_calculo') . ",";
                                                // Array de Sessao para Resumo do Calculo
                                                //$arquivo_escrita = fopen ( $nome_arquivo, "a" );
                                                #echo '<br><b>ESCREVE NO ARQUIVO</b>';
                                                fwrite( $arquivo,  $strTMP );

                                                $rsCalculos->proximo();
                                            }

                                            $obTAdministracaoAuditoria->setDado( "numcgm", Sessao::read( "numCgm" ) );
                                            $obTAdministracaoAuditoria->setDado( "cod_acao", Sessao::read( "acao" ) );
                                            $obTAdministracaoAuditoria->setDado( "timestamp", $stTimestampAtual );
                                            $obTAdministracaoAuditoria->setDado( "objeto", $stCalculos );
                                            $obTAdministracaoAuditoria->setDado( "transacao", false );
                                            $obTAdministracaoAuditoria->inclusao( $boTransacao );
                                        }
                                    }
                                }//FIM CALCULO TRIBUTARIO
                                if ( Sessao::read( 'TipoCalculo' ) == "individual" && $obErro->ocorreu() ) {
                                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
                                    exit;
                                }
                            }// se a abertura de Transacao ocorreu OK

                            fclose ( $arquivo  );
                            fclose ( $arquivo2 );
                            // fecha transação
                            #echo '<br>FECHA TRANSACAO<hr>';

                            #usleep(500000);
                            #echo " Memoria:".round((memory_get_usage()/1024)/1024)." MB";

                            // passa ao proximo imovel a ser calculado
                            $rsImoveis->proximo();
                        }

                    }

                }

            $this->obTransacao->fechaTransacao ( $boFlagTransacao , $boTransacao , $obErro );
                Sessao::write( 'arquivo_calculos', $nome_arquivo );
                Sessao::write( 'arquivo_calculos_lancamentos', $nome_arquivo2 );

                // lançamento do calculo ( sera verificado se usuario pediu lançamento )
                if ( Sessao::read( 'TipoCalculo' ) == "individual" && $_REQUEST['efetuar_lancamentos'] == 'sim' && !$obErro->ocorreu() && !$boErroCalculo ) {
                    //VERIFICAMOS SE É CALCULO INDIVIDUAL.
                    //Se for, chama a funcao e realiza o lançamento no momento
                    if ( $this->obRARRGrupo->getCodGrupo() ) {
                        $obConexao = new Conexao;
                        $inCodGrupo = $this->obRARRGrupo->getCodGrupo();
                        $inExercicio = $this->getExercicio();

                        $inInscricaoInicial = 1000000;
                        $inInscricaoFinal = 0;
                        if ( Sessao::read( 'arquivo_calculos_lancamentos' ) ) {
                            $nome_arquivo = Sessao::read( 'arquivo_calculos_lancamentos' );
                            if ( $arquivo = fopen ( $nome_arquivo, 'r' ) ) {
                                $arCalculo = array ();
                                $arDados = array();
                                while (!feof($arquivo)) {
                                    if ($stLinha = fgets($arquivo)) {
                                        $arLinha = explode ('&', $stLinha);
                                        $arDados[] = $arLinha[2];
                                    }
                                }

                                fclose( $arquivo );

                                sort( $arDados, SORT_NUMERIC );

                                $inInscricaoFinal = $arDados[count( $arDados )-1];
                                $inInscricaoInicial = $arDados[0];
                            }
                        }

                        $stVencimento = "";
                        $stTipoDesconto = "";
                        $stValorDesconto = "";
                        $stVencimentoDesconto = "";
                        $stNumeroParcela = "";
                        if ( !Sessao::read( "UsaCalendarioFiscal" ) ) {
                            $arSessaoParcelas = Sessao::read( "parcelas" );
                            $inQtdParc = count( $arSessaoParcelas );
                            for ( $inX=0; $inX<count( $arSessaoParcelas ); $inX++ ) {
                                if ($inX > 0) {
                                    $stVencimento .= ";";
                                    $stTipoDesconto .= ";";
                                    $stValorDesconto .= ";";
                                    $stVencimentoDesconto .= ";";
                                    $stNumeroParcela .= ";";
                                }

                                $arTMPVenc = explode( "/", $arSessaoParcelas[$inX]["data_vencimento"] );
                                $stVencimento .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];
                                $stVencimentoDesconto .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];

                                $stValorDesconto .= (double) $arSessaoParcelas[$inX]["valor"];

                                if ($arSessaoParcelas[$inX]["stTipoDesconto"] == "Percentual") {
                                    $stTipoDesconto .= "true";
                                }else
                                    $stTipoDesconto .= "false";

                                if ($arSessaoParcelas[$inX]["stTipoParcela"] == "Única") {
                                    $stNumeroParcela .= "0";
                                }else
                                    $stNumeroParcela .= $arSessaoParcelas[$inX]["stTipoParcela"];
                            }
                        } else {
                            $inQtdParc = -1;
                        }

                        $stSql = " SELECT
                                        CASE WHEN cod_modulo = 12 THEN
                                            1
                                        ELSE
                                            CASE WHEN cod_modulo = 14 THEN
                                                2
                                            ELSE
                                                3
                                            END
                                        END AS tipo
                                    FROM
                                        arrecadacao.grupo_credito
                                    WHERE cod_grupo = ".$inCodGrupo." AND ano_exercicio = '".$inExercicio."'";

                        $obErro = $obConexao->executaSQL( $rsTipo, $stSql, $boTransacao );
                        if ( $obErro->ocorreu() )
                            return $obErro;

                        $stSql = " SELECT arrecadacao.fn_lancamento_manual_por_grupo_intervalo( ".$inCodGrupo.", ".$inExercicio.", '".$inInscricaoInicial." AND ".$inInscricaoFinal."', ".$inQtdParc.", '".$stVencimento."', '".$stTipoDesconto."', '".$stValorDesconto."', '".$stVencimentoDesconto."', '".$stNumeroParcela."', ".$rsTipo->getCampo("tipo")." )  AS resultado;";
//echo "comando = ".$stSql."<br>";exit;
                        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
                        if ( !$rsRecordSet->Eof() ) {
                            $this->obRARRLancamento = new RARRLancamento( $this );
                            Sessao::write( "lancamentos_cods", $rsRecordSet->getCampo("resultado") );
                            $arTMP = explode( ",", $rsRecordSet->getCampo("resultado") );
                            $this->obRARRLancamento->inCodLancamento = $arTMP[0];
                        }

                        if ( $obErro->ocorreu() )
                            return $obErro;
                    } else {//if ( $this->obRARRGrupo->getCodGrupo() ) {
                        //por credito
                        $obConexao   = new Conexao;
                        $arCredito = explode ('.', $this->getChaveCredito());

                        $inInscricaoInicial = 1000000;
                        $inInscricaoFinal = 0;
                        if ( Sessao::read( 'arquivo_calculos_lancamentos' ) ) {

                            $nome_arquivo = Sessao::read( 'arquivo_calculos_lancamentos' );
                            if ( $arquivo = fopen ( $nome_arquivo, 'r' ) ) {
                                $arCalculo = array ();
                                $arDados = array();
                                while (!feof($arquivo)) {
                                    if ($stLinha = fgets($arquivo)) {
                                        $arLinha = explode ('&', $stLinha);
                                        $arDados[] = $arLinha[2];
                                    }
                                }

                                fclose( $arquivo );

                                sort( $arDados, SORT_NUMERIC );

                                $inInscricaoFinal = $arDados[count( $arDados )-1];
                                $inInscricaoInicial = $arDados[0];
                            }
                        }

                        $stVencimento = "";
                        $stTipoDesconto = "";
                        $stValorDesconto = "";
                        $stVencimentoDesconto = "";
                        $stNumeroParcela = "";
                        if ( !Sessao::read( "UsaCalendarioFiscal" ) ) {
                            $arSessaoParcelas = Sessao::read( "parcelas" );
                            $inQtdParc = count( $arSessaoParcelas );
                            for ( $inX=0; $inX<count( $arSessaoParcelas ); $inX++ ) {
                                if ($inX > 0) {
                                    $stVencimento .= ";";
                                    $stTipoDesconto .= ";";
                                    $stValorDesconto .= ";";
                                    $stVencimentoDesconto .= ";";
                                    $stNumeroParcela .= ";";
                                }

                                $arTMPVenc = explode( "/", $arSessaoParcelas[$inX]["data_vencimento"] );
                                $stVencimento .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];
                                $stVencimentoDesconto .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];

                                $stValorDesconto .= (double) $arSessaoParcelas[$inX]["valor"];

                                if ($arSessaoParcelas[$inX]["stTipoDesconto"] == "Percentual") {
                                    $stTipoDesconto .= "true";
                                }else
                                    $stTipoDesconto .= "false";

                                if ($arSessaoParcelas[$inX]["stTipoParcela"] == "Única") {
                                    $stNumeroParcela .= "0";
                                }else
                                    $stNumeroParcela .= $arSessaoParcelas[$inX]["stTipoParcela"];
                            }
                        } else {
                            $inQtdParc = -1;
                        }

                        $stSql = " SELECT arrecadacao.fn_lancamento_manual_por_credito_intervalo( ".$arCredito[0].", ".$arCredito[1].", ".$arCredito[2].", ".$arCredito[3].", ".Sessao::read( "exercicio" ).", '".$inInscricaoInicial." AND ".$inInscricaoFinal."', ".$inQtdParc.", '".$stVencimento."', '".$stTipoDesconto."', '".$stValorDesconto."', '".$stVencimentoDesconto."', '".$stNumeroParcela."', 1 )  AS resultado;";
                        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
                        if ( !$rsRecordSet->Eof() ) {
                            $this->obRARRLancamento = new RARRLancamento( $this );
                            Sessao::write( "lancamentos_cods", $rsRecordSet->getCampo("resultado") );
                            $arTMP = explode( ",", $rsRecordSet->getCampo("resultado") );
                            $this->obRARRLancamento->inCodLancamento = $arTMP[0];
                        }

                        if ( $obErro->ocorreu() )
                            return $obErro;
                    }
                }//if ( $sessao->transf6['TipoCalculo'] == "individual" && $_REQUEST['efetuar_lancamentos'] == 'sim' && !$obErro->ocorreu() && !$boErroCalculo ) {

            endif; // << if2
        endif; // << if1

        return $obErro;
    }

    /* calculo do economico */
    public function calculoEconomico($boTransacao = false)
    {
        if (!is_object($obErro))
            $obErro = new Erro;

        if (!$obErro->ocorreu()): // >> if1
            Sessao::write( 'calculos', '' );
            Sessao::write( 'arCalculoErro', array() );
            $obErro = $this->obRCEMInscricaoEconomica->listarInscricaoConsulta( $rsEmpresas, $boTransacao );

        if (!$obErro->ocorreu() && $rsEmpresas->getNumLinhas() > 0)	: // >> if2

            $arCalculos = array ();

            $nome_arquivo  = "calculos_".date("Y-m-d").'_'.date("h-i-s");
            $nome_arquivo  = "/tmp/".$nome_arquivo.'.txt';

            $nome_arquivo2  = "lancamentos_".date("Y-m-d").'_'.date("h-i-s");
            $nome_arquivo2  = "/tmp/".$nome_arquivo2.'.txt';

            set_time_limit (0);
            $boFlagTransacao = true;
            $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);

            while ( !$rsEmpresas->eof() ) { #WHILE DAS EMPRESAS

                $IE = $rsEmpresas->getCampo('inscricao_economica');
                #echo '<hr>INSCRICAO ECONOMICA: '. $IE;
                #exit;

                if ( $rsEmpresas->getCampo('situacao') != 'Ativo' ) {
                    $rsEmpresas->proximo();
                } else {

//				    $boFlagTransacao = false;
    //			    $boTransacao = "";
        //		    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
                    if ( $obErro->ocorreu() ) {
                        break;
                    } else {

                        $arquivo = fopen ( $nome_arquivo, "a" );
                        $arquivo2 = fopen ( $nome_arquivo2, "a" );

                        if (!$arquivo) {
                            $obErro->setDescricao("Não foi possível criar arquivo necessário para realizar cálculo.");
                        } else {

                            $this->obCalculoTributario->setDado('inRegistro',$IE);
                            $this->obCalculoTributario->setDado('inExercicio', $this->getExercicio());
                            $this->obCalculoTributario->setDado('stGrupo', $this->obRARRGrupo->getCodGrupo());
                            $this->obCalculoTributario->setDado('stCredito', $this->getChaveCredito());
                            $this->obCalculoTributario->setDado('stModulo', 14);

                            $obErro = $this->obCalculoTributario->calculoTributario( $rsCalculo, $boTransacao );
                            #$this->obCalculoTributario->debug(); #exit;
                            $rsCalculo->setPrimeiroElemento();
//                          echo '<br>CALCULOS:'.$rsCalculo->getCampo('retorno').'<br>';

                            if ( $obErro->ocorreu() ) {
                                #echo '<h1> BREAK </h1>';
                                break;
                            } else {

                                if ( $rsCalculo->getCampo('retorno') == 'f' || $rsCalculo->getNumLinhas() < 1 ) {
                                    if ( Sessao::read( 'TipoCalculo' ) == "individual" ) {
                                        $this->buscarCalculosMensagem($rsCalculosMensagens, $boTransacao);

                                        if ( $rsCalculosMensagens->getNumLinhas() > 0 ) {
                                            $obErro->setDescricao( $rsCalculosMensagens->getCampo('mensagem') );
                                        } else {
                                            $obErro->setDescricao("Não foi possível realizar Cálculo Individual para a inscrição Econômica [<b>".$IE."</b>]");
                                        }

                                    } else {
                                        $arCalculoErroSessao = Sessao::read( "arCalculoErro" );
                                        $this->buscarCalculosErros ( $rsCalculosErro , $boTransacao );
                                        while (!$rsCalculosErro->eof()) {
                                            $Erro = ($rsCalculosErro->getCampo( 'erro' ) == 't') ? "Sim" : "Não";
                                            $arCalculoErroSessao[] = array(
                                                'registro' => $rsCalculosErro->getCampo( 'registro' ),
                                                'credito' => $rsCalculosErro->getCampo( 'credito' ),
                                                'funcao' => $rsCalculosErro->getCampo( 'funcao' ),
                                                'erro' => $Erro ,
                                                'valor' => number_format($rsCalculosErro->getCampo( 'valor' ),2,',','.')
                                            );
                                            $rsCalculosErro->proximo();
                                        }

                                        Sessao::write( "arCalculoErro", $arCalculoErroSessao );
                                    }
                                    $boErroCalculo = true;
                            } else {
                                // listar calculos
                                $this->buscarCalculos($rsCalculos, $boTransacao);
                                if ( $rsCalculos->getNumLinhas() < 1 ) {
                                    if ( Sessao::read( 'TipoCalculo' ) == "individual" ) {
                                        $this->buscarCalculosMensagem($rsCalculosMensagens, $boTransacao);

                                        if ( $rsCalculosMensagens->getNumLinhas() > 0 ) {
                                            $obErro->setDescricao( $rsCalculosMensagens->getCampo('mensagem') );
                                        } else {
                                            $obErro->setDescricao("Nenhum cálculo encontrado para a inscricao");
                                        }
                                    }
                                } else {
                                    // acrescentar imovel a lista de calculos
                                    $rsCalculos->setPrimeiroElemento();
                                    $arCalculos = array ();
                                    $stCalculosSessao = Sessao::read( "calculos" );

                                    $obTAdministracaoAuditoria = new TAuditoria;
                                    $stTimestampAtual = "";
                                    while (!$rsCalculos->eof()) {
                                        if ( $stTimestampAtual == $rsCalculos->getCampo("timestamp") ) {
                                            $stCalculos .= ",".$rsCalculos->getCampo("cod_calculo");
                                        } else {
                                            if ($stTimestampAtual) {
                                                $obTAdministracaoAuditoria->setDado( "numcgm", Sessao::read( "numCgm" ) );
                                                $obTAdministracaoAuditoria->setDado( "cod_acao", Sessao::read( "acao" ) );
                                                $obTAdministracaoAuditoria->setDado( "timestamp", $stTimestampAtual );
                                                $obTAdministracaoAuditoria->setDado( "objeto", $stCalculos );
                                                $obTAdministracaoAuditoria->setDado( "transacao", false );
                                                $obTAdministracaoAuditoria->inclusao( $boTransacao );
                                            }

                                            $stCalculos = "cod_calculo=".$rsCalculos->getCampo("cod_calculo");
                                            $stTimestampAtual = $rsCalculos->getCampo("timestamp");
                                        }
                                        //--------------------------------------

                                        if ( $rsCalculos->getCampo('cod_calculo') ) {
                                            $arCalculos[] = array (
                                                'cod_calculo' => $rsCalculos->getCampo('cod_calculo'),
                                                'valor' => $rsCalculos->getCampo('valor'),
                                                'inscricao_economica' => $IE );
                                            // Array de Sessao para Resumo do Calculo
                                            $stCalculosSessao .= $rsCalculos->getCampo('cod_calculo') . ",";

                                            $strTMPLanc  = $rsCalculos->getCampo('cod_calculo').'&';
                                            $strTMPLanc .= $rsCalculos->getCampo('valor').'&';
                                            $strTMPLanc .= $IE."\n";

                                            fwrite( $arquivo2,  $strTMPLanc );
                                        }
                                        $strTMP = $rsCalculos->getCampo('cod_calculo') . ",";
                                        //$sessao->transf3['calculos'] .= $strTMP;
                                        //$arquivo_escrita = fopen ( $nome_arquivo, "a" );
                                        #echo '<br><b>ESCREVE NO ARQUIVO</b> '.$strTMP;
                                        fwrite( $arquivo,  $strTMP );

                                        $rsCalculos->proximo();
                                    }// while dos calculos_correntes

                                    Sessao::write( "calculos", $stCalculosSessao );
                                }//if CALCULOS > 0
                            }//FIm SE O RETORNO EH TRUE DO CALCULO TRIBUTARIO
                        } //FIM DO CALCULO TRIBUTARIO

                        if ( Sessao::read( 'TipoCalculo' ) == "individual" && $obErro->ocorreu() ) {
                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
                            exit;
                        }

                    }// se a abertura de transação ocorreu OK

                    fclose ( $arquivo  );
                    fclose ( $arquivo2 );

//                    $this->obTransacao->fechaTransacao ( $boFlagTransacao , $boTransacao , $obErro , $this->obTARRCalculo );

                    $rsEmpresas->proximo();
                }
            }
        }

        $this->obTransacao->fechaTransacao ( $boFlagTransacao , $boTransacao , $obErro );
        Sessao::write( 'arquivo_calculos', $nome_arquivo );
        Sessao::write( 'arquivo_calculos_lancamentos', $nome_arquivo2 );

        #echo '<br>verificando se é calculo individual';
        // lançamento do calculo ( sera verificado se usuario pediu lançamento )
        if ( Sessao::read( 'TipoCalculo' ) == "individual" && $_REQUEST['efetuar_lancamentos'] == 'sim' && !$obErro->ocorreu() && !$boErroCalculo ) {
            if ( $this->obRARRGrupo->getCodGrupo() ) {
                $obConexao   = new Conexao;
                $inCodGrupo = $this->obRARRGrupo->getCodGrupo();
                $inExercicio = $this->getExercicio();

                $inInscricaoInicial = 1000000;
                $inInscricaoFinal = 0;
                if ( Sessao::read( 'arquivo_calculos_lancamentos' ) ) {

                    $nome_arquivo = Sessao::read( 'arquivo_calculos_lancamentos' );
                    if ( $arquivo = fopen ( $nome_arquivo, 'r' ) ) {
                        $arCalculo = array ();
                        $arDados = array();
                        while (!feof($arquivo)) {
                            if ($stLinha = fgets($arquivo)) {
                                $arLinha = explode ('&', $stLinha);
                                $arDados[] = $arLinha[2];
                            }
                        }

                        fclose( $arquivo );

                        sort( $arDados, SORT_NUMERIC );

                        $inInscricaoFinal = $arDados[count( $arDados )-1];
                        $inInscricaoInicial = $arDados[0];
                    }
                }

                $stVencimento = "";
                $stTipoDesconto = "";
                $stValorDesconto = "";
                $stVencimentoDesconto = "";
                $stNumeroParcela = "";
                if ( !Sessao::read( "UsaCalendarioFiscal" ) ) {
                    $arParcelasSessao = Sessao::read( "parcelas" );
                    $inQtdParc = count( $arParcelasSessao );
                    for ( $inX=0; $inX<count( $arParcelasSessao ); $inX++ ) {
                        if ($inX > 0) {
                            $stVencimento .= ";";
                            $stTipoDesconto .= ";";
                            $stValorDesconto .= ";";
                            $stVencimentoDesconto .= ";";
                            $stNumeroParcela .= ";";
                        }

                        $arTMPVenc = explode( "/", $arParcelasSessao[$inX]["data_vencimento"] );
                        $stVencimento .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];
                        $stVencimentoDesconto .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];

                        $stValorDesconto .= (double) $arParcelasSessao[$inX]["valor"];

                        if ($arParcelasSessao[$inX]["stTipoDesconto"] == "Percentual") {
                            $stTipoDesconto .= "true";
                        }else
                            $stTipoDesconto .= "false";

                        if ($arParcelasSessao[$inX]["stTipoParcela"] == "Única") {
                            $stNumeroParcela .= "0";
                        }else
                            $stNumeroParcela .= $arParcelasSessao[$inX]["stTipoParcela"];
                    }
                } else {
                    $inQtdParc = -1;
                }

                $stSql = " SELECT
                                CASE WHEN cod_modulo = 12 THEN
                                    1
                                ELSE
                                    CASE WHEN cod_modulo = 14 THEN
                                        2
                                    ELSE
                                        3
                                    END
                                END AS tipo
                            FROM
                                arrecadacao.grupo_credito
                            WHERE cod_grupo = ".$inCodGrupo." AND ano_exercicio = ".$inExercicio;

                $obErro = $obConexao->executaSQL( $rsTipo, $stSql, $boTransacao );
                if ( $obErro->ocorreu() )
                    return $obErro;

                $stSql = " SELECT arrecadacao.fn_lancamento_manual_por_grupo_intervalo( ".$inCodGrupo.", ".$inExercicio.", '".$inInscricaoInicial." AND ".$inInscricaoFinal."', ".$inQtdParc.", '".$stVencimento."', '".$stTipoDesconto."', '".$stValorDesconto."', '".$stVencimentoDesconto."', '".$stNumeroParcela."', ".$rsTipo->getCampo("tipo")." )  AS resultado;";
                $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
                if ( !$rsRecordSet->Eof() ) {
                    $this->obRARRLancamento = new RARRLancamento( $this );
                    Sessao::write( "lancamentos_cods", $rsRecordSet->getCampo("resultado") );
                    $arTMP = explode( ",", $rsRecordSet->getCampo("resultado") );
                    $this->obRARRLancamento->inCodLancamento = $arTMP[0];
                }

                if ( $obErro->ocorreu() )
                    return $obErro;
            } else {//if ( $this->obRARRGrupo->getCodGrupo() ) {
                //por credito
                $obConexao   = new Conexao;
                $arCredito = explode ('.', $this->getChaveCredito());

                $inInscricaoInicial = 1000000;
                $inInscricaoFinal = 0;
                if ( Sessao::read( 'arquivo_calculos_lancamentos' ) ) {
                    $nome_arquivo = Sessao::read( 'arquivo_calculos_lancamentos' );
                    if ( $arquivo = fopen ( $nome_arquivo, 'r' ) ) {
                        $arCalculo = array ();
                        $arDados = array();
                        while (!feof($arquivo)) {
                            if ($stLinha = fgets($arquivo)) {
                                $arLinha = explode ('&', $stLinha);
                                $arDados[] = $arLinha[2];
                            }
                        }

                        fclose( $arquivo );

                        sort( $arDados, SORT_NUMERIC );

                        $inInscricaoFinal = $arDados[count( $arDados )-1];
                        $inInscricaoInicial = $arDados[0];
                    }
                }

                $stVencimento = "";
                $stTipoDesconto = "";
                $stValorDesconto = "";
                $stVencimentoDesconto = "";
                $stNumeroParcela = "";
                if ( !Sessao::read( "UsaCalendarioFiscal" ) ) {
                    $arParcelasSessao = Sessao::read( "parcelas" );
                    $inQtdParc = count( $arParcelasSessao );
                    for ( $inX=0; $inX<count( $arParcelasSessao ); $inX++ ) {
                        if ($inX > 0) {
                            $stVencimento .= ";";
                            $stTipoDesconto .= ";";
                            $stValorDesconto .= ";";
                            $stVencimentoDesconto .= ";";
                            $stNumeroParcela .= ";";
                        }

                        $arTMPVenc = explode( "/", $arParcelasSessao[$inX]["data_vencimento"] );
                        $stVencimento .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];
                        $stVencimentoDesconto .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];

                        $stValorDesconto .= (double) $arParcelasSessao[$inX]["valor"];

                        if ($arParcelasSessao[$inX]["stTipoDesconto"] == "Percentual") {
                            $stTipoDesconto .= "true";
                        }else
                            $stTipoDesconto .= "false";

                        if ($arParcelasSessao[$inX]["stTipoParcela"] == "Única") {
                            $stNumeroParcela .= "0";
                        }else
                            $stNumeroParcela .= $arParcelasSessao[$inX]["stTipoParcela"];
                    }
                } else {
                    $inQtdParc = -1;
                }

                switch ($this->obRModulo->getCodModulo()) {
                    case 12 :
                        $inTipoCalculo = 1;
                        break;

                    case 14 :
                        $inTipoCalculo = 2;
                        break;

                    default:
                        $inTipoCalculo = 3;
                        break;
                }

                $stSql = " SELECT arrecadacao.fn_lancamento_manual_por_credito_intervalo( ".$arCredito[0].", ".$arCredito[1].", ".$arCredito[2].", ".$arCredito[3].", ".Sessao::read( "exercicio" ).", '".$inInscricaoInicial." AND ".$inInscricaoFinal."', ".$inQtdParc.", '".$stVencimento."', '".$stTipoDesconto."', '".$stValorDesconto."', '".$stVencimentoDesconto."', '".$stNumeroParcela."', ".$inTipoCalculo." )  AS resultado;";
                $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
                if ( !$rsRecordSet->Eof() ) {
                    $this->obRARRLancamento = new RARRLancamento( $this );
                    Sessao::write( "lancamentos_cods", $rsRecordSet->getCampo("resultado") );
                    $arTMP = explode( ",", $rsRecordSet->getCampo("resultado") );
                    $this->obRARRLancamento->inCodLancamento = $arTMP[0];
                }

                if ( $obErro->ocorreu() )
                    return $obErro;
            }
        }

        endif; // << if2
    endif; // << if1

return $obErro;
}

    /**
    * Executar Calculo
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function executarCalculo($boTransacao = "", $boITBI = false)
    {
        include_once (CAM_GT_ARR_MAPEAMENTO . "FARRUltimoValorVenal.class.php");
        Sessao::write( 'calculos', "" );
        Sessao::write( "lancamentos_cods", "" );
        $this->obRARRLancamento = new RARRLancamento($this);
        //$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $obErro = new Erro;
        if (!$obErro->ocorreu()) {

            switch ($this->obRModulo->getCodModulo()) {
                case 12 :

                    $obErro = $this->obRCIMImovel->listarImoveisConsulta($rsImoveis, $boTransacao);
                    /*verifica se filtro retornou imoveis*/
                    if ($rsImoveis->getNumLinhas() >= 1) {
                        // Laço dos Imoveis --
                        while (!$rsImoveis->eof()) {

                            //verifica se o imovel nao esta baixado
                            $this->obRCIMImovel->setNumeroInscricao($rsImoveis->getCampo('inscricao_municipal'));
                            $this->obRCIMImovel->verificaBaixaImovel( $rsBaixaImovel, $boTransacao );
                            if ($rsBaixaImovel->getNumLinhas() > -1) {
                                if ($rsImoveis->getNumLinhas() == 1) {
                                    $obErro->setDescricao("A Inscrição Municipal " . $this->obRCIMImovel->getNumeroInscricao() . " está baixada!");
                                }

                            } else {
                                if (!$boITBI) {
                                    $boFlagTransacao = $boTransacao = false;
                                    $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
                                }
                                $this->abreCalculo($boTransacao);

                                // inicializar variaveis de ValorVenal e Array/Retorno do Calculo
                                $arCalculoImovel = array ();
                                $nuVenalPredial = $nuVenalTerreno = 0;

                                // recuperar cgm do proprietarios
                                $this->obRCIMImovel->setNumeroInscricao($rsImoveis->getCampo("inscricao_municipal"));
                                $obErro = $this->obRCIMImovel->consultaProprietariosCalculo($rsProprietario, $boTransacao);

                                // Laço de calculo de creditos!  -- Recomendo neste momento: System of a Down -  Violent Pornography
                                if ($this->obRARRGrupo->arRMONCredito)
                                    $this->arCreditos = $this->obRARRGrupo->arRMONCredito;

                                foreach ($this->arCreditos as $obRMONCredito) {
                                    //monta nome objeto da funcao do credito à calcular.
                                    require_once (CAM_GT_ARR_MAPEAMENTO . 'FCalculo.class.php');
                                    $obFuncao = new FCalculo;
                                    $obFuncao->stFuncao = $obRMONCredito->getNomeFuncao();

                                    //executa funcao do credito e guarda valor
                                    $inInscricaoMunicipal = $rsImoveis->getCampo("inscricao_municipal");
                                    $obErro = $obFuncao->executaFuncao($rsRetornoFuncao, $inInscricaoMunicipal . "," . $obRMONCredito->roRARRGrupo->getExercicio(), $boTransacao);
                                    $nuRetorno = $rsRetornoFuncao->getCampo("valor");

                                    //if ($stFuncao == "FVenalEdificacao")
                                    if ($obRMONCredito->getValorCorrespondente() == 'venal_predial')
                                        $nuVenalPredial = $nuRetorno;
                                    elseif ($obRMONCredito->getValorCorrespondente() == 'venal_terreno') $nuVenalTerreno = $nuRetorno;

                                    if (!$obErro->ocorreu()) {
                                        // grava valores calculados
                                        $obErro = $this->obTARRCalculo->proximoCod($this->inCodCalculo, $boTransacao);
                                        if (!$obErro->ocorreu()) {
                                            $this->obTARRCalculo->setDado("cod_calculo", $this->getCodCalculo());
                                            $this->obTARRCalculo->setDado("cod_credito", $obRMONCredito->getCodCredito());
                                            $this->obTARRCalculo->setDado("cod_especie", $obRMONCredito->getCodEspecie());
                                            $this->obTARRCalculo->setDado("cod_genero", $obRMONCredito->getCodGenero());
                                            $this->obTARRCalculo->setDado("cod_natureza", $obRMONCredito->getCodNatureza());
                                            $this->obTARRCalculo->setDado("exercicio", $obRMONCredito->roRARRGrupo->getExercicio());
                                            $this->obTARRCalculo->setDado("valor", $nuRetorno);
                                            $this->obTARRCalculo->setDado("nro_parcelas", $this->getNumParcelas());
                                            $this->obTARRCalculo->setDado("calculado", true);

                                            $arCalculoImovel[] = array (
                                            "cod_calculo" => $this->getCodCalculo(), "inscricao_municipal" => $inInscricaoMunicipal, "numcgm" => $rsProprietario->getCampo("numcgm"), "valor" => $nuRetorno, "cod_credito" => $obRMONCredito->getCodCredito());
                                            // salvar calculos efetuadas na sessao, para uso do relatorio.
                                            $stCalculoSessao = Sessao::read( "calculos" );
                                            $stCalculoSessao = $stCalculoSessao.$this->getCodCalculo() . ",";

                                            $obErro = $this->obTARRCalculo->inclusao($boTransacao);

                                            if (!$obErro->ocorreu()) {
                                                $obTARRCalculoCgm = new TARRCalculoCgm;
                                                $rsProprietario->setPrimeiroElemento();
                                                while (!$rsProprietario->eof()) {
                                                    $obTARRCalculoCgm->setDado("cod_calculo", $this->getCodCalculo());
                                                    $obTARRCalculoCgm->setDado("numcgm", $rsProprietario->getCampo("numcgm"));
                                                    $obErro = $obTARRCalculoCgm->inclusao($boTransacao);
                                                    if ($obErro->ocorreu())
                                                        break;
                                                    $rsProprietario->proximo();
                                                }
                                            }

                                            // se for calculo de grupo, insere na tabela arrecadacao.calculo_grupo_credito
                                            if (!$obErro->ocorreu() && $obRMONCredito->roRARRGrupo->getCodGrupo()) {

                                                // TABELA CALCULO GRUPO CREDITO
                                                $this->obTARRCalculoGrupoCredito->setDado("cod_calculo", $this->getCodCalculo());
                                                $this->obTARRCalculoGrupoCredito->setDado("cod_grupo", $obRMONCredito->roRARRGrupo->getCodGrupo());
                                                $obErro = $this->obTARRCalculoGrupoCredito->inclusao($boTransacao);

                                                if (!$obErro->ocorreu()) {
                                                    $obErro = $this->salvaLogCalculo($this->getCodCalculo(), $boTransacao);
                                                    if ($obErro->ocorreu()) {
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } // fim do laço dos creditos (foreach)
                                if (!$obErro->ocorreu()) {
                                    foreach ($arCalculoImovel as $arCalculo) {
                                        $this->obTARRImovelCalculo->setDado("cod_calculo", $arCalculo["cod_calculo"]);
                                        $this->obTARRImovelCalculo->setDado("inscricao_municipal", $arCalculo["inscricao_municipal"]);
                                        $obErro = $this->obTARRImovelCalculo->inclusao($boTransacao);
                                        if ($obErro->ocorreu())
                                            break;
                                    }
                                    if (!$obErro->ocorreu() && $this->boLancamento == 'true') {
                                        //$this->obRARRLancamento = new RARRLancamento($this);
                                        $this->obRARRLancamento->refCalculo($this);
                                        $this->obRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo($this->obRARRGrupo->getCodGrupo());
                                        if ($this->boTipoCalculo) { // true é geral
                                            $obErro = $this->obRARRLancamento->efetuarLancamento($boTransacao, $arCalculoImovel);
                                        } else { // false é individual/parcial
                                            $obErro = $this->obRARRLancamento->efetuarLancamentoParcialIndividual($boTransacao, $arCalculoImovel);
                                        }
                                    }
                                }
                                $this->fechaCalculo($boTransacao);
                                if (!$boITBI)
                                    $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalculo);
                            }
                            $rsImoveis->proximo();
                        } // fim laço imoveis
                        // retirar ultima virgula dos calculo
                    } else { // fim if verificacao imoveil
                        $obErro->setDescricao("Imóveis não localizados na base em função do filtro utilizado!");
                    }
                    //
                    break;
                case 14 :
                    break;

            }
        }
        //$this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalculo );
        //echo "<br>Uso de Memoria final:".round((memory_get_usage()/1024)/1024)." MB";
        return $obErro;

    }

    public function executarCalculoCredito($boTransacao = "")
    {
        $boFlagTransacao = false;
        //$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        Sessao::write( 'calculos', "" );
        Sessao::write( "lancamentos_cods", "" );

        $obErro = new Erro;
        if (!$obErro->ocorreu()) {
            $obErro = $this->obRCIMImovel->listarImoveisConsulta($rsImoveis, $boTransacao);
            /*verifica se filtro retornou imoveis*/
            if ($rsImoveis->getNumLinhas() >= 1) {
                // Laço dos Imoveis
                while (!$rsImoveis->eof()) {

                    //verifica se o imovel nao esta baixado
                    $this->obRCIMImovel->setNumeroInscricao($rsImoveis->getCampo('inscricao_municipal'));
                    $this->obRCIMImovel->verificaBaixaImovel($rsBaixaImovel, $boTransacao);
                    if ($rsBaixaImovel->getNumLinhas() > -1) {
                        if ($rsImoveis->getNumLinhas() == 1) {
                            $obErro->setDescricao("A Inscrição Municipal " . $this->obRCIMImovel->getNumeroInscricao() . " está baixada!");
                        }

                    } else {
                        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
                        $this->abreCalculo($boTransacao);
                        // inicializar variaveis de ValorVenal e Array/Retorno do Calculo
                        $arCalculoImovel = array ();
                        $nuVenalPredial = $nuVenalTerreno = 0;

                        // recuperar cgm do proprietarios
                        $obRCIMImovel = new RCIMImovel(new RCIMLote);
                        $obRCIMImovel->setNumeroInscricao($rsImoveis->getCampo("inscricao_municipal"));
                        $obErro = $obRCIMImovel->consultaProprietariosCalculo($rsProprietario, $boTransacao);

                        // Laço de calculo de creditos!
                        if ($this->obRARRGrupo->arRMONCredito)
                            $this->arCreditos = $this->obRARRGrupo->arRMONCredito;

                        foreach ($this->arCreditos as $obRMONCredito) {

                            //monta nome objeto da funcao do credito à calcular
                            require_once (CAM_GT_ARR_MAPEAMENTO . 'FCalculo.class.php');
                            $obFuncao = new FCalculo;
                            $obFuncao->stFuncao = $obRMONCredito->getNomeFuncao();

                            //executa funcao do credito e guarda valor
                            $inInscricaoMunicipal = $rsImoveis->getCampo("inscricao_municipal");
                            $stParametros = $inInscricaoMunicipal . "," . $this->getExercicio();

                            $obErro = $obFuncao->executaFuncao($rsRetornoFuncao, $stParametros, $boTransacao);
                            $nuRetorno = $rsRetornoFuncao->getCampo("valor");

                            if ($obRMONCredito->getValorCorrespondente() == 'venal_predial')
                                $nuVenalPredial = $nuRetorno;
                            elseif ($obRMONCredito->getValorCorrespondente() == 'venal_terreno') $nuVenalTerreno = $nuRetorno;
                            //grava valores calculados
                            $obErro = $this->obTARRCalculo->proximoCod($this->inCodCalculo, $boTransacao);
                            if (!$obErro->ocorreu()) {
                                $this->obTARRCalculo->setDado("cod_calculo", $this->getCodCalculo());
                                $this->obTARRCalculo->setDado("cod_credito", $obRMONCredito->getCodCredito());
                                $this->obTARRCalculo->setDado("cod_especie", $obRMONCredito->getCodEspecie());
                                $this->obTARRCalculo->setDado("cod_genero", $obRMONCredito->getCodGenero());
                                $this->obTARRCalculo->setDado("cod_natureza", $obRMONCredito->getCodNatureza());
                                $this->obTARRCalculo->setDado("numcgm", $this->obRCGM->getNumCGM());
                                $this->obTARRCalculo->setDado("exercicio", $this->getExercicio());
                                $this->obTARRCalculo->setDado("valor", $nuRetorno);
                                $this->obTARRCalculo->setDado("nro_parcelas", $this->getNumParcelas());
                                $this->obTARRCalculo->setDado("calculado", true);

                                //$arCalculoImovel[] = $this->getCodCalculo();
                                $obErro = $this->obTARRCalculo->inclusao($boTransacao);
                                //$this->obTARRCalculo->debug();
                                $stCalculosSessao = Sessao::read( "calculos" );
                                $stCalculosSessao = $stCalculosSessao . $this->getCodCalculo() . ",";
                                Sessao::write( "calculos", $stCalculosSessao );

                                if (!$obErro->ocorreu()) {
                                    $obTARRCalculoCgm = new TARRCalculoCgm;
                                    $rsProprietario->setPrimeiroElemento();
                                    while (!$rsProprietario->eof()) {
                                        $obTARRCalculoCgm->setDado("cod_calculo", $this->getCodCalculo());
                                        $obTARRCalculoCgm->setDado("numcgm", $rsProprietario->getCampo("numcgm"));
                                        $obErro = $obTARRCalculoCgm->inclusao($boTransacao);
                                        if ($obErro->ocorreu())
                                            break;
                                        $rsProprietario->proximo();
                                    }
                                }

                                if (!$obErro->ocorreu()) {
                                    //insere na relacao imovel_calculo
                                    $this->obRCIMImovel->setNumeroInscricao($rsImoveis->getCampo("inscricao_municipal"));
                                    $obErro = $this->recuperaTimestampImovelCalculo($rsTimestampImovel, $boTransacao);
                                    $timestamp = $rsTimestampImovel->getCampo('timestamp');
                                    if (!$obErro->ocorreu()) {
                                        $this->obTARRImovelCalculo->setDado("cod_calculo", $this->getCodCalculo());
                                        $this->obTARRImovelCalculo->setDado("inscricao_municipal", $rsImoveis->getCampo("inscricao_municipal"));
                                        //  $this->obTARRImovelCalculo->setDado("timestamp"             , $timestamp );
                                        $obErro = $this->obTARRImovelCalculo->inclusao($boTransacao);
                                        //$this->obTARRImovelCalculo->debug();
                                    }

                                    // salva log do calculo
                                    if (!$obErro->ocorreu()) {
                                        $obErro = $this->salvaLogCalculo($this->getCodCalculo(), $boTransacao);
                                        // após salvar log efetua lançamento caso este esteja selecionado!
                                        if (!$obErro->ocorreu() && $this->boLancamento == 'true') {
                                            $obErro = $this->obRARRLancamento->efetuarLancamentoCredito($boTransacao);
                                        }
                                    }
                                }
                            }
                        } // fim do laço dos creditos (foreach)
                        $this->fechaCalculo($boTransacao);
                        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalculo);
                    }
                    $rsImoveis->proximo();
                } // fim laço imoveis
            } else { // fim if verificacao imoveil
                $obErro->setDescricao("Imóveis não localizados na base em função do filtro utilizado!");
            }
        }

        return $obErro;
    }

    /**
    * Abre Calculo
    * @access Private
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function abreCalculo($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        if (!$obErro->ocorreu()) {
            $obErro = $this->obFARRAbreCalculo->executaFuncao($boTransacao);
        }
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalculo);

        return $obErro;
    }
    /**
    * Fecha Calculo - registra timestamp, grava valores na tabela de log de calculo e dropa tabela temporaria
    * @access Private
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function fechaCalculo($boTransacao = "") { // antes teriamos que salvar todos os valores que estao tabela temporaria
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        if (!$obErro->ocorreu()) {
            $obErro = $this->obFARRFechaCalculo->executaFuncao($boTransacao);
        }
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalculo);

        return $obErro;
    }
    /**
    * Abre Calculo
    * @access Private
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function salvaLogCalculo($inCodCalculo, $boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        if (!$obErro->ocorreu()) {
            $obCalculoTemp = new FValorLogTemp;
            $obCalculoTemp->executaFuncao($rsLog, "", $boTransacao);

            $stValor = $rsLog->getCampo("valor");

            $this->obTARRLogCalculo->setDado("cod_calculo", $inCodCalculo);
            $this->obTARRLogCalculo->setDado("valor", $stValor);
            $obErro = $this->obTARRLogCalculo->inclusao($boTransacao);

            if (!$obErro->ocorreu()) {
                $this->obTARRLogTemp->exclusao($boTransacao);
            }
        }
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTARRCalculo);

        return $obErro;
    }

    /**
     * Lista os Calculos efetuados
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function listarCalculos(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->obRARRGrupo->obRMONCredito->getCodCredito()) {
            $stFiltro .= " C.cod_credito IN ( " . $this->obRARRGrupo->obRMONCredito->getCodCredito() . " ) AND ";
        }
        if ($this->getCodCalculo()) {
            $stFiltro .= " C.cod_calculo = " . $this->getCodCalculo() . " AND ";
        }
        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }
        $stOrdem = " ORDER BY cod_calculo ";
        $obErro = $this->obTARRCalculo->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }
    public function listarCalculosGrupo(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->obRARRGrupo->getCodGrupo() && $this->obRARRGrupo->getExercicio() ) {
            $stFiltro .= " calculo_grupo_credito.cod_grupo = " . $this->obRARRGrupo->getCodGrupo() . "  AND ";
            $stFiltro .= " calculo_grupo_credito.ano_exercicio = '" . $this->obRARRGrupo->getExercicio() . "'  AND ";
        }
        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }
        $stOrdem = " ORDER BY IC.inscricao_municipal, cod_calculo ";
        $obErro = $this->obTARRCalculo->recuperaCalculosGrupo($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }

    public function buscaSomaCalculos(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->obRARRGrupo->getCodGrupo() && $this->obRARRGrupo->getExercicio() ) {
            $stFiltro .= " acgc.cod_grupo = " . $this->obRARRGrupo->getCodGrupo() . "  AND ";
            $stFiltro .= " acgc.ano_exercicio = " . $this->obRARRGrupo->getExercicio() . "  AND ";
        }

        if ( $this->getCodCalculo() ) {
            $stFiltro .= " acgc.cod_calculo = " . $this->getCodCalculo() . "  AND ";
            $stFiltro2 = $this->getCodCalculo();
        }

        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }
        $stOrdem = " ORDER BY cod_calculo ";
        $obErro = $this->obTARRCalculo->recuperaSomaCalculos($rsRecordSet, $stFiltro, $stFiltro2, $stOrdem, $boTransacao);
        #$this->obTARRCalculo->debug();

        return $obErro;
    }
    /**
     * Lista os Calculos efetuados
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function listarCalculosLancamento(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->obRARRGrupo->obRMONCredito->getCodCredito()) {
            $stFiltro .= " C.cod_credito IN ( " . $this->obRARRGrupo->obRMONCredito->getCodCredito() . " ) AND ";
        }
        if ($this->obRARRGrupo->obRMONCredito->getCodEspecie()) {
            $stFiltro .= " C.cod_especie IN ( " . $this->obRARRGrupo->obRMONCredito->getCodEspecie() . " ) AND ";
        }
        if ($this->obRARRGrupo->obRMONCredito->getCodGenero()) {
            $stFiltro .= " C.cod_genero  IN ( " . $this->obRARRGrupo->obRMONCredito->getCodGenero() . " ) AND ";
        }
        if ($this->obRARRGrupo->obRMONCredito->getCodNatureza()) {
            $stFiltro .= " C.cod_natureza IN ( " . $this->obRARRGrupo->obRMONCredito->getCodNatureza() . " ) AND ";
        }
        if ($this->getCodCalculo()) {
            $stFiltro .= " C.cod_calculo = " . $this->getCodCalculo() . " AND ";
        }
        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }
        $stOrdem = " ORDER BY cod_calculo ";

        if ( $this->obRModulo->getCodModulo() == 14 )
            $obErro = $this->obTARRCalculo->recuperaCalculosLancamentoEconomico($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
        else
            $obErro = $this->obTARRCalculo->recuperaCalculosLancamento($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
        //$this->obTARRCalculo->debug();
        return $obErro;
    }

    /**
     * Busca o valor do calculo por inscricao municipal
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function buscaValorCalculo(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
                $stOrdem="";
        if ($this->obRARRGrupo->obRMONCredito->getCodCredito()) {
            $stFiltro .= " C.cod_credito IN ( " . $this->obRARRGrupo->obRMONCredito->getCodCredito() . " ) AND ";
        }
        if ($this->getCodCalculo()) {
            $stFiltro .= " C.cod_calculo = " . $this->getCodCalculo() . " AND ";
        }
        if ($this->obRCIMImovel->getNumeroInscricao()) {
            $stFiltro .= " IC.inscricao_municipal = " . $this->obRCIMImovel->getNumeroInscricao() . " AND ";
        }
        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }
        $obErro = $this->obTARRCalculo->recuperaValorCalculo($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }
    /**
     * Busca o valor do calculo por inscricao municipal - CREDITO
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function buscaValorCalculoCredito(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->obRARRGrupo->obRMONCredito->getCodCredito()) {
            $stFiltro .= " C.cod_credito IN ( " . $this->obRARRGrupo->obRMONCredito->getCodCredito() . " ) AND ";
        }
        if ($this->getCodCalculo()) {
            $stFiltro .= " C.cod_calculo = " . $this->getCodCalculo() . " AND ";
        }

        if ($this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
            $stFiltro .= " CEC.inscricao_economica = ".$this->obRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
        }

        if ($this->obRCIMImovel->getNumeroInscricao()) {
            $stFiltro .= " IC.inscricao_municipal = " . $this->obRCIMImovel->getNumeroInscricao() . " AND ";
        }
        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }
        if ( $this->obRModulo->getCodModulo() == 14 )
            $obErro = $this->obTARRCalculo->recuperaValorCalculoCreditoEconomico($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
        else
            $obErro = $this->obTARRCalculo->recuperaValorCalculoCredito($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
        //$this->obTARRCalculo->debug();
        return $obErro;
    }

    /**
     * Lista os imoveis que contem calculo
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function listarImovelCalculo(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->obRARRGrupo->obRMONCredito->getCodCredito()) {
            $stFiltro .= " cod_credito IN ( " . $this->obRARRGrupo->obRMONCredito->getCodCredito() . " ) AND ";
        }
        if ($this->obRCIMImovel->getNumeroInscricao()) {
            $stFiltro = " inscricao_municipal =  " . $this->obRCIMImovel->getNumeroInscricao()." AND";
        }
        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }
        $stOrdem = " ORDER BY inscricao_municipal ";
        $obErro = $this->obTARRImovelCalculo->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }

/**
     * Lista os imoveis que contem calculo
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function listarCalculosAbertoImovel(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->obRCIMImovel->getNumeroInscricao()) {
            $stFiltro = " aic.inscricao_municipal =  " . $this->obRCIMImovel->getNumeroInscricao()." AND\n";
        }
        $stFiltro .= " apag.numeracao is null AND\n";
        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }

        $stOrdem = "
            GROUP BY
                calc.cod_calculo
                , aic.inscricao_municipal
                , accgm.numcgm
                , alc.cod_lancamento
            ORDER BY
                aic.inscricao_municipal, alc.cod_lancamento, calc.cod_calculo
        ";

        $obErro = $this->obTARRImovelCalculo->recuperaCalculosAbertoImovel($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;

    }

    /**
     * Recupera max(timestamp) do imovel na tabela imovel_calculo
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function recuperaTimestampImovelCalculo(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->obRCIMImovel->getNumeroInscricao()) {
            $stFiltro = " WHERE IC.inscricao_municipal =  " . $this->obRCIMImovel->getNumeroInscricao();
        }
        $obErro = $this->obTARRImovelCalculo->recuperaTimestampImovelCalculo($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }
    /**
     * Recupera max(timestamp) do imovel na tabela imovel_v_venal
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function recuperaTimestampImovelVenal(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->obRCIMImovel->getNumeroInscricao()) {
            $stFiltro = " WHERE IC.inscricao_municipal =  " . $this->obRCIMImovel->getNumeroInscricao();
        }
        $obErro = $this->obTARRImovelCalculo->recuperaTimestampImovelVenal($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }

    public function listarRelatorioExecucao(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->getCodCalculo()) {
            $stFiltro .= " ac.cod_calculo in (" . $this->getCodCalculo() . ")  AND ";
        }
        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE " . substr($stFiltro, 0, -4);
        }
        //$stOrdem = " ORDER BY nom_cgm, inscricao, cod_calculo ";
        $stOrdem = " ORDER BY inscricao ";
        $obErro = $this->obTARRCalculo->recuperaListaRelatorioExecucao($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
//		$this->obTARRCalculo->debug(); exit;
        return $obErro;
    }
    public function listarConsulta(& $rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->obRCIMImovel->getNumeroInscricao()) {
            $stFiltro .= " \n\t and aic.inscricao_municipal = " . $this->obRCIMImovel->getNumeroInscricao() . "";
        }

        $obErro = $this->obTARRCalculo->recuperaListaConsulta($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }

    public function buscarCalculos(& $rsCalculos, $boTransacao = "")
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $stSql = "
            select calculos_correntes.cod_calculo
                 , calculos_correntes.valor
                 , calculo.timestamp
              from calculos_correntes
        inner join arrecadacao.calculo
                on calculo.cod_calculo = calculos_correntes.cod_calculo;
        ";
        $obErro = $obConexao->executaSql($rsCalculos, $stSql, $boTransacao);

        return $obErro;
    }

    public function buscarCalculosErros(& $rsCalculos, $boTransacao = "")
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $stSql = " select * from calculos_erro";
        $obErro = $obConexao->executaSql($rsCalculos, $stSql, $boTransacao);

        return $obErro;
    }

    public function buscarCalculosMensagem(& $rsCalculos, $boTransacao = "")
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $stSql = " select cod_calculo , mensagem from calculos_mensagem; ";
        $obErro = $obConexao->executaSql($rsCalculos, $stSql, $boTransacao);

        return $obErro;
    }

    public function listarGruposSimulacao(&$rsGrupos, $stFiltro = '', $boTransacao = '')
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $stSql = 'select DISTINCT cod_grupo, ano_exercicio, descricao from
                    arrecadacao.calculo_grupo_credito
                   INNER JOIN arrecadacao.calculo
                     USING(cod_calculo)
                   INNER JOIN arrecadacao.grupo_credito
                     USING(ano_exercicio, cod_grupo)
                   WHERE simulado = true '.$stFiltro;

        $obErro = $obConexao->executaSql($rsGrupos, $stSql, $boTransacao);
    }

} // fecha classe
?>
