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

    * Classe de Regra do Relatório de despesa total com pessoal
    * Data de Criação   : 08/08/2014
    *
    * @author Analista:      
    * @author Desenvolvedor: Arthur Cruz
    *
    * @ignore
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioDespesaTotalPessoal.class.php");
include_once (CAM_GPC_STN_MAPEAMENTO . 'TSTNDespesaPessoal.class.php');

class RTCEMGRelatorioDespesaTotalPessoal{
    /**
    * @var Array
    * @access Private
    */
    var $arCodEntidades;
    /**
    * @var String
    * @access Private
    */
    var $stDataInicial;
    /**
    * @var String
    * @access Private
    */
    var $stDataFinal;
    /**
    * @var String
    * @access Private
    */
    var $stExercicio;
    /**
    * @var String
    * @access Private
    */
    var $stTipoSituacao;
    /**
    * @var String
    * @access Private
    */
    var $stTipoConsulta;
     /**
    * @var String
    * @access Private
    */
    var $stExercicioRestos;
    
    public function getCodEntidades() { return $this->arCodEntidades; }
    public function setCodEntidades( $arCodEntidades ) { $this->arCodEntidades = $arCodEntidades; }
    
    public function getDataInicial() { return $this->stDataInicial; }
    public function setDataInicial( $stDataInicial ) { $this->stDataInicial = $stDataInicial; }
    
    public function getDataFinal() { return $this->stDataFinal; }
    public function setDataFinal( $stDataFinal ) { $this->stDataFinal = $stDataFinal; }
    
    public function getExercicio() { return $this->stExercicio; }
    public function setExercicio( $stExercicio ) { $this->stExercicio = $stExercicio; }
    
    public function getTipoSituacao() { return $this->stTipoSituacao; }
    public function setTipoSituacao( $stTipoSituacao ) { $this->stTipoSituacao = $stTipoSituacao; }

    public function getTipoConsulta() { return $this->stTipoConsulta; }
    public function setTipoConsulta( $stTipoConsulta ) { $this->stTipoConsulta = $stTipoConsulta; }
    
    public function getExercicioRestos() { return $this->stExercicioRestos; }
    public function setExercicioRestos( $stExercicioRestos ) { $this->stExercicioRestos = $stExercicioRestos; }
    
    /**
    * Método Construtor
    * @access Private
    */
    public function RTCEMGRelatorioDespesaTotalPessoal()
    {
        
    }
    
    /**
    * Método abstrato
    * @access Public
    */
    function geraRecordSet(&$rsRecordSet , $stOrder = "")
    {
        $rsDespesas = new RecordSet();
        $rsDespesasExclusoes = new RecordSet();
        $rsDespesas2013 = new RecordSet();
    
        list( $dia, $mes, $ano ) = explode( "/", $this->getDataInicial() );
        if($this->getExercicio() == '2014' ) {
            $obTSTNDespesaPessoal = new  TSTNDespesaPessoal();
            $obTSTNDespesaPessoal->setDado( "exercicio"         , $this->getExercicio() );
            $obTSTNDespesaPessoal->setDado( "cod_entidades"     , $this->getCodEntidades());
            $obTSTNDespesaPessoal->setDado( "mes"               , (int)$mes);
            $obTSTNDespesaPessoal->listValorEntidade( $rsDespesas2013 );  $obTSTNDespesaPessoal->debug();

            $inCountDespesas2013 = 1;
            $arDemostrativoDespesa2013 = array();

            foreach( $rsDespesas2013->getElementos() as $arDespesas2013 )
            {
 
               $arDemostrativoDespesa2013["nom_conta"]    = 'DESPESAS TOTAL COM PESSOAL EM 2013';
               $arDemostrativoDespesa2013["entidade"]     = $arDespesas2013["cod_entidade"];
               $vlTotalDespesas2013Total = $vlTotalDespesas2013Total+ $arDespesas2013["valor"];
               
               if ($_REQUEST['stPeriodicidade' ] == "Mes"){
                  $inMes = $arDespesas2013["mes"] - $mes;
               } elseif ($_REQUEST['stPeriodicidade' ] == "Bimestre") {
                  $inMes = $arDespesas2013["mes"]-($mes+1);
               } elseif ($_REQUEST['stPeriodicidade' ] == "Trimestre") {
                  $inMes = $arDespesas2013["mes"]-($mes+2);
               } elseif ($_REQUEST['stPeriodicidade' ] == "Quadrimestre") {
                  $inMes = $arDespesas2013["mes"]-($mes+3);
               } elseif ($_REQUEST['stPeriodicidade' ] == "Semestre") {
                 $inMes = $arDespesas2013["mes"] - ($mes+5);
               }
               
               $idMes = 'mes_'.$inMes;
               switch ($arDespesas2013["mes"]) {
                   case 1:
                       $arDemostrativoDespesa2013[$idMes]          = number_format($arDespesas2013["valor"] , 2, ',','.');  
                       $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 2:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"] , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 3:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"] , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 4:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"] , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break;  
                    case 5:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"] , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 6:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"] , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 7:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"] , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 8:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"]  , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 9:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"]  , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 10:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"]  , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
                    case 11:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"]  , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                     case 12:
                        $arDemostrativoDespesa2013[$idMes]         = number_format($arDespesas2013["valor"]  , 2, ',','.');
                        $arDespesa2013[$idMes] =$arDespesas2013["valor"];
                    break; 
               }
               $arDemostrativoDespesa2013["total"] = number_format($vlTotalDespesas2013Total  , 2, ',','.');
             
               $rsDespesas2013->proximo();
            }
            
            $inCountDespesas2013 = 0;            
        }
        
        $vlTotalDespesas2013Mes1  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_1"] != "" ) ? $arDespesa2013["mes_1"]:0;
        $vlTotalDespesas2013Mes2  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_2"] != "" ) ? $arDespesa2013["mes_2"]:0;
        $vlTotalDespesas2013Mes3  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_3"] != "" ) ? $arDespesa2013["mes_3"]:0;
        $vlTotalDespesas2013Mes4  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_4"] != "" ) ? $arDespesa2013["mes_4"]:0;
        $vlTotalDespesas2013Mes5  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_5"] != "" ) ? $arDespesa2013["mes_5"]:0;
        $vlTotalDespesas2013Mes6  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_6"] != "" ) ? $arDespesa2013["mes_6"]:0;
        $vlTotalDespesas2013Mes7  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_7"] != "" ) ? $arDespesa2013["mes_7"]:0;
        $vlTotalDespesas2013Mes8  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_8"] != "" ) ? $arDespesa2013["mes_8"]:0;
        $vlTotalDespesas2013Mes9  = ($this->getExercicio() == 2014 && $arDespesa2013["mes_9"] != "" ) ? $arDespesa2013["mes_9"]:0;
        $vlTotalDespesas2013Mes10 = ($this->getExercicio() == 2014 && $arDespesa2013["mes_10"] != "" ) ? $arDespesa2013["mes_10"]:0;
        $vlTotalDespesas2013Mes11 = ($this->getExercicio() == 2014 && $arDespesa2013["mes_11"] != "" ) ? $arDespesa2013["mes_11"]:0;
        $vlTotalDespesas2013Mes12 = ($this->getExercicio() == 2014 && $arDespesa2013["mes_12"] != "" ) ? $arDespesa2013["mes_12"]:0;        

        $obTTCEMGRelatorioDespesaTotalPessoal = new TTCEMGRelatorioDespesaTotalPessoal();
                
        // Montando tabela de despesas.
        // Só vai trazer despesas sem as exclusoes tipo_despesa 1.
        $obTTCEMGRelatorioDespesaTotalPessoal->setDado( "exercicio"         , $this->getExercicio() );
        $obTTCEMGRelatorioDespesaTotalPessoal->setDado( "dt_inicial"        , $this->getDataInicial() );
        $obTTCEMGRelatorioDespesaTotalPessoal->setDado( "dt_final"          , $this->getDataFinal() );
        $obTTCEMGRelatorioDespesaTotalPessoal->setDado( "cod_entidades"     , $this->getCodEntidades());
        $obTTCEMGRelatorioDespesaTotalPessoal->setDado( "tipo_despesa"      , 1);
        $obTTCEMGRelatorioDespesaTotalPessoal->setDado( "tipo_situacao"     , $this->getTipoSituacao());
        $obTTCEMGRelatorioDespesaTotalPessoal->recuperaDespesaTotalPessoal( $rsDespesas );

        $vlTotalDespesasMes1  = 0;
        $vlTotalDespesasMes2  = 0;
        $vlTotalDespesasMes3  = 0;
        $vlTotalDespesasMes4  = 0;
        $vlTotalDespesasMes5  = 0;
        $vlTotalDespesasMes6  = 0;
        $vlTotalDespesasMes7  = 0;
        $vlTotalDespesasMes8  = 0;
        $vlTotalDespesasMes9  = 0;
        $vlTotalDespesasMes10 = 0;
        $vlTotalDespesasMes11 = 0;
        $vlTotalDespesasMes12 = 0;
        $vlTotalDespesasTotal = 0;
        
        $inCountDespesas = 1;
        $arDemostrativoDespesa = array();
        while( !$rsDespesas->eof() )
        {
            
            $arDemostrativoDespesa[$inCountDespesas]["nom_conta"]     = $rsDespesas->getCampo("nom_conta");
            $arDemostrativoDespesa[$inCountDespesas]["cod_estrutural"]= $rsDespesas->getCampo("cod_estrutural");
            $arDemostrativoDespesa[$inCountDespesas]["mes_1"]         = number_format($rsDespesas->getCampo("mes_1") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_2"]         = number_format($rsDespesas->getCampo("mes_2") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_3"]         = number_format($rsDespesas->getCampo("mes_3") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_4"]         = number_format($rsDespesas->getCampo("mes_4") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_5"]         = number_format($rsDespesas->getCampo("mes_5") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_6"]         = number_format($rsDespesas->getCampo("mes_6") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_7"]         = number_format($rsDespesas->getCampo("mes_7") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_8"]         = number_format($rsDespesas->getCampo("mes_8") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_9"]         = number_format($rsDespesas->getCampo("mes_9") , 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_10"]        = number_format($rsDespesas->getCampo("mes_10"), 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_11"]        = number_format($rsDespesas->getCampo("mes_11"), 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["mes_12"]        = number_format($rsDespesas->getCampo("mes_12"), 2, ',','.');
            $arDemostrativoDespesa[$inCountDespesas]["total"]         = number_format($rsDespesas->getCampo("total") , 2, ',','.');
            
            // Em total não deve ser deduzido os valores da linha 'Correspondente ao período de apuração/móvel'
            if ( $rsDespesas->getCampo("cod_estrutural") != "88888888" ) {
                $vlTotalDespesasMes1  = $vlTotalDespesasMes1  + $rsDespesas->getCampo("mes_1");
                $vlTotalDespesasMes2  = $vlTotalDespesasMes2  + $rsDespesas->getCampo("mes_2");
                $vlTotalDespesasMes3  = $vlTotalDespesasMes3  + $rsDespesas->getCampo("mes_3");
                $vlTotalDespesasMes4  = $vlTotalDespesasMes4  + $rsDespesas->getCampo("mes_4");
                $vlTotalDespesasMes5  = $vlTotalDespesasMes5  + $rsDespesas->getCampo("mes_5");
                $vlTotalDespesasMes6  = $vlTotalDespesasMes6  + $rsDespesas->getCampo("mes_6");
                $vlTotalDespesasMes7  = $vlTotalDespesasMes7  + $rsDespesas->getCampo("mes_7");
                $vlTotalDespesasMes8  = $vlTotalDespesasMes8  + $rsDespesas->getCampo("mes_8");
                $vlTotalDespesasMes9  = $vlTotalDespesasMes9  + $rsDespesas->getCampo("mes_9");
                $vlTotalDespesasMes10 = $vlTotalDespesasMes10 + $rsDespesas->getCampo("mes_10");
                $vlTotalDespesasMes11 = $vlTotalDespesasMes11 + $rsDespesas->getCampo("mes_11");
                $vlTotalDespesasMes12 = $vlTotalDespesasMes12 + $rsDespesas->getCampo("mes_12");
                $vlTotalDespesasTotal = $vlTotalDespesasTotal + $rsDespesas->getCampo("total");
            }
            $inCountDespesas++;
            
            $rsDespesas->proximo();
        }
        
        $inCountDespesas = 0;
        
        // MONTA TOTAIS POR MES E TOTAL DE DESPESAS
        $arDemostrativoDespesaTotal[$inCountDespesas]["nom_conta"] = "TOTAL";
        $arDemostrativoDespesaTotal[$inCountDespesas]["cod_estrutural"] = "";
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_1"]  = number_format($vlTotalDespesasMes1 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_2"]  = number_format($vlTotalDespesasMes2 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_3"]  = number_format($vlTotalDespesasMes3 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_4"]  = number_format($vlTotalDespesasMes4 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_5"]  = number_format($vlTotalDespesasMes5 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_6"]  = number_format($vlTotalDespesasMes6 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_7"]  = number_format($vlTotalDespesasMes7 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_8"]  = number_format($vlTotalDespesasMes8 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_9"]  = number_format($vlTotalDespesasMes9 , 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_10"] = number_format($vlTotalDespesasMes10, 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_11"] = number_format($vlTotalDespesasMes11, 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["mes_12"] = number_format($vlTotalDespesasMes12, 2, ',','.');
        $arDemostrativoDespesaTotal[$inCountDespesas]["total"]  = number_format($vlTotalDespesasTotal, 2, ',','.');
        
        // Montando tabela de despesas.
        // Só vai trazer as exclusoes tipo_despesa 2.
        $obTTCEMGRelatorioDespesaTotalPessoal->setDado("tipo_despesa", 2);
        $obTTCEMGRelatorioDespesaTotalPessoal->recuperaDespesaTotalPessoal( $rsDespesasExclusoes );
        
        $vlTotalDespesasExclusoesMes1  = 0;
        $vlTotalDespesasExclusoesMes2  = 0;
        $vlTotalDespesasExclusoesMes3  = 0;
        $vlTotalDespesasExclusoesMes4  = 0;
        $vlTotalDespesasExclusoesMes5  = 0;
        $vlTotalDespesasExclusoesMes6  = 0;
        $vlTotalDespesasExclusoesMes7  = 0;
        $vlTotalDespesasExclusoesMes8  = 0;
        $vlTotalDespesasExclusoesMes9  = 0;
        $vlTotalDespesasExclusoesMes10 = 0;
        $vlTotalDespesasExclusoesMes11 = 0;
        $vlTotalDespesasExclusoesMes12 = 0;
        $vlTotalDespesasExclusoesTotal = 0;
        
        $inCountDespesasExclusoes = 1;
        $arDemostrativoDespesaExclusoes = array();
        while( !$rsDespesasExclusoes->eof() )
        {
            
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["nom_conta"]     = $rsDespesasExclusoes->getCampo("nom_conta");
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["cod_estrutural"]= $rsDespesasExclusoes->getCampo("cod_estrutural");
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_1"]         = number_format($rsDespesasExclusoes->getCampo("mes_1") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_2"]         = number_format($rsDespesasExclusoes->getCampo("mes_2") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_3"]         = number_format($rsDespesasExclusoes->getCampo("mes_3") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_4"]         = number_format($rsDespesasExclusoes->getCampo("mes_4") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_5"]         = number_format($rsDespesasExclusoes->getCampo("mes_5") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_6"]         = number_format($rsDespesasExclusoes->getCampo("mes_6") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_7"]         = number_format($rsDespesasExclusoes->getCampo("mes_7") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_8"]         = number_format($rsDespesasExclusoes->getCampo("mes_8") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_9"]         = number_format($rsDespesasExclusoes->getCampo("mes_9") , 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_10"]        = number_format($rsDespesasExclusoes->getCampo("mes_10"), 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_11"]        = number_format($rsDespesasExclusoes->getCampo("mes_11"), 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["mes_12"]        = number_format($rsDespesasExclusoes->getCampo("mes_12"), 2, ',','.');
            $arDemostrativoDespesaExclusoes[$inCountDespesasExclusoes]["total"]         = number_format($rsDespesasExclusoes->getCampo("total") , 2, ',','.');
            
            // Em total não deve ser deduzido os valores da linha 'Correspondente ao período de apuração/móvel'
            if ( $rsDespesas->getCampo("cod_estrutural") != "88888888" ) {
                $vlTotalDespesasExclusoesMes1  = $vlTotalDespesasExclusoesMes1  + $rsDespesasExclusoes->getCampo("mes_1");
                $vlTotalDespesasExclusoesMes2  = $vlTotalDespesasExclusoesMes2  + $rsDespesasExclusoes->getCampo("mes_2");
                $vlTotalDespesasExclusoesMes3  = $vlTotalDespesasExclusoesMes3  + $rsDespesasExclusoes->getCampo("mes_3");
                $vlTotalDespesasExclusoesMes4  = $vlTotalDespesasExclusoesMes4  + $rsDespesasExclusoes->getCampo("mes_4");
                $vlTotalDespesasExclusoesMes5  = $vlTotalDespesasExclusoesMes5  + $rsDespesasExclusoes->getCampo("mes_5");
                $vlTotalDespesasExclusoesMes6  = $vlTotalDespesasExclusoesMes6  + $rsDespesasExclusoes->getCampo("mes_6");
                $vlTotalDespesasExclusoesMes7  = $vlTotalDespesasExclusoesMes7  + $rsDespesasExclusoes->getCampo("mes_7");
                $vlTotalDespesasExclusoesMes8  = $vlTotalDespesasExclusoesMes8  + $rsDespesasExclusoes->getCampo("mes_8");
                $vlTotalDespesasExclusoesMes9  = $vlTotalDespesasExclusoesMes9  + $rsDespesasExclusoes->getCampo("mes_9");
                $vlTotalDespesasExclusoesMes10 = $vlTotalDespesasExclusoesMes10 + $rsDespesasExclusoes->getCampo("mes_10");
                $vlTotalDespesasExclusoesMes11 = $vlTotalDespesasExclusoesMes11 + $rsDespesasExclusoes->getCampo("mes_11");
                $vlTotalDespesasExclusoesMes12 = $vlTotalDespesasExclusoesMes12 + $rsDespesasExclusoes->getCampo("mes_12");
                $vlTotalDespesasExclusoesTotal = $vlTotalDespesasExclusoesTotal + $rsDespesasExclusoes->getCampo("total");
            }
            
            $inCountDespesasExclusoes++;
            
            $rsDespesasExclusoes->proximo();
        }
        
        $inCountDespesasExclusoes = 0;
        // MONTA TOTAIS POR MES E TOTAL DE EXCLUSOES DE DESPESAS
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["nom_conta"] = "TOTAL";
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["cod_estrutural"] = "";
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_1"]  = number_format($vlTotalDespesasExclusoesMes1 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_2"]  = number_format($vlTotalDespesasExclusoesMes2 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_3"]  = number_format($vlTotalDespesasExclusoesMes3 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_4"]  = number_format($vlTotalDespesasExclusoesMes4 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_5"]  = number_format($vlTotalDespesasExclusoesMes5 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_6"]  = number_format($vlTotalDespesasExclusoesMes6 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_7"]  = number_format($vlTotalDespesasExclusoesMes7 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_8"]  = number_format($vlTotalDespesasExclusoesMes8 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_9"]  = number_format($vlTotalDespesasExclusoesMes9 , 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_10"] = number_format($vlTotalDespesasExclusoesMes10, 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_11"] = number_format($vlTotalDespesasExclusoesMes11, 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["mes_12"] = number_format($vlTotalDespesasExclusoesMes12, 2, ',','.');
        $arDemostrativoDespesaExclusoesTotal[$inCountDespesasExclusoes]["total"]  = number_format($vlTotalDespesasExclusoesTotal, 2, ',','.');

        $vlTotaisDespesaMes1  = ($vlTotalDespesasMes1  - $vlTotalDespesasExclusoesMes1)+$vlTotalDespesas2013Mes1;
        $vlTotaisDespesaMes2  = ($vlTotalDespesasMes2  - $vlTotalDespesasExclusoesMes2)+$vlTotalDespesas2013Mes2;
        $vlTotaisDespesaMes3  = ($vlTotalDespesasMes3  - $vlTotalDespesasExclusoesMes3)+$vlTotalDespesas2013Mes3;
        $vlTotaisDespesaMes4  = ($vlTotalDespesasMes4  - $vlTotalDespesasExclusoesMes4)+$vlTotalDespesas2013Mes4;
        $vlTotaisDespesaMes5  = ($vlTotalDespesasMes5  - $vlTotalDespesasExclusoesMes5)+$vlTotalDespesas2013Mes5;
        $vlTotaisDespesaMes6  = ($vlTotalDespesasMes6  - $vlTotalDespesasExclusoesMes6)+$vlTotalDespesas2013Mes6;
        $vlTotaisDespesaMes7  = ($vlTotalDespesasMes7  - $vlTotalDespesasExclusoesMes7)+$vlTotalDespesas2013Mes7;
        $vlTotaisDespesaMes8  = ($vlTotalDespesasMes8  - $vlTotalDespesasExclusoesMes8)+$vlTotalDespesas2013Mes8;
        $vlTotaisDespesaMes9  = ($vlTotalDespesasMes9  - $vlTotalDespesasExclusoesMes9)+$vlTotalDespesas2013Mes9;
        $vlTotaisDespesaMes10 = ($vlTotalDespesasMes10 - $vlTotalDespesasExclusoesMes10)+$vlTotalDespesas2013Mes10;
        $vlTotaisDespesaMes11 = ($vlTotalDespesasMes11 - $vlTotalDespesasExclusoesMes11)+$vlTotalDespesas2013Mes11;
        $vlTotaisDespesaMes12 = ($vlTotalDespesasMes12 - $vlTotalDespesasExclusoesMes12)+$vlTotalDespesas2013Mes12;
        $vlTotaisDespesaTotal = ($vlTotalDespesasTotal - $vlTotalDespesasExclusoesTotal) + $vlTotalDespesas2013Total;
              
        $arValorTotalDespesaPessoal[0]["nom_conta"]      = "DESPESAS TOTAL COM PESSOAL";
        $arValorTotalDespesaPessoal[0]["cod_estrutural"] = "";
        $arValorTotalDespesaPessoal[0]["mes_1"]  = number_format(($vlTotaisDespesaMes1 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_2"]  = number_format(($vlTotaisDespesaMes2 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_3"]  = number_format(($vlTotaisDespesaMes3 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_4"]  = number_format(($vlTotaisDespesaMes4 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_5"]  = number_format(($vlTotaisDespesaMes5 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_6"]  = number_format(($vlTotaisDespesaMes6 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_7"]  = number_format(($vlTotaisDespesaMes7 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_8"]  = number_format(($vlTotaisDespesaMes8 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_9"]  = number_format(($vlTotaisDespesaMes9 ), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_10"] = number_format(($vlTotaisDespesaMes10), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_11"] = number_format(($vlTotaisDespesaMes11), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["mes_12"] = number_format(($vlTotaisDespesaMes12), 2, ',','.');
        $arValorTotalDespesaPessoal[0]["total"]  = number_format(($vlTotaisDespesaTotal), 2, ',','.');
        
        $rsRecordSet["arDespesas"]                 = $arDemostrativoDespesa;
        $rsRecordSet["arDespesasTotal"]            = $arDemostrativoDespesaTotal;
        $rsRecordSet["arDespesasExclusoes"]        = $arDemostrativoDespesaExclusoes;
        $rsRecordSet["arDespesasExclusoesTotal"]   = $arDemostrativoDespesaExclusoesTotal;
        $rsRecordSet["arValorTotalDespesaPessoal"] = $arValorTotalDespesaPessoal;      
        $rsRecordSet["arDespesas2013"]   = $arDemostrativoDespesa2013;
        
    }
}
?>