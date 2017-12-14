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

    * Classe de Regra do Relatório de Demostrativo RCL
    * Data de Criação   : 08/08/2014
    *

   * @author Analista:      Eduardo Paculski Schitz
   * @author Desenvolvedor: Franver Sarmento de Moraes
   *
   * @ignore
   * $Id: RTCEMGRelatorioDemostrativoRCL.class.php 62534 2015-05-18 19:27:57Z lisiane $
   * $Date: 2015-05-18 16:27:57 -0300 (Mon, 18 May 2015) $
   * $Author: lisiane $
   * $Rev: 62534 $
   *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioDemostrativoRCL.class.php");

class RTCEMGRelatorioDemostrativoRCL {
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
    public function RTCEMGRelatorioDemostrativoRCL()
    {
        
    }
    
    /**
    * Método abstrato
    * @access Public
    */
    function geraRecordSet(&$rsRecordSet , $stOrder = "")
    {
        $rsReceitas = new RecordSet();
        $rsReceitasExclusoes = new RecordSet();
        $rsDespesas = new RecordSet();
        $rsDespesasDeducoes = new RecordSet();

        $obTTCEMGRelatorioDemostrativoRCL = new TTCEMGRelatorioDemostrativoRCL();
        $obTTCEMGRelatorioDemostrativoRCL->setDado( "exercicio"         , $this->getExercicio() );
        $obTTCEMGRelatorioDemostrativoRCL->setDado( "dt_inicial"        , $this->getDataInicial() );
        $obTTCEMGRelatorioDemostrativoRCL->setDado( "dt_final"          , $this->getDataFinal() );
        $obTTCEMGRelatorioDemostrativoRCL->setDado( "cod_entidades"     , $this->getCodEntidades());
        $obTTCEMGRelatorioDemostrativoRCL->setDado( "tipo_despesa"      , 1);
        $obTTCEMGRelatorioDemostrativoRCL->setDado( "tipo_situacao"     , $this->getTipoSituacao());
        $obTTCEMGRelatorioDemostrativoRCL->setDado( "exercicio_restos"  , $this->getExercicioRestos());
        $obTTCEMGRelatorioDemostrativoRCL->recuperaReceitasDemonstrativoRCL( $rsReceitas );

        // Inicio da tabela Receitas.
        $vlTotalMes1  = 0;
        $vlTotalMes2  = 0;
        $vlTotalMes3  = 0;
        $vlTotalMes4  = 0;
        $vlTotalMes5  = 0;
        $vlTotalMes6  = 0;
        $vlTotalMes7  = 0;
        $vlTotalMes8  = 0;
        $vlTotalMes9  = 0;
        $vlTotalMes10 = 0;
        $vlTotalMes11 = 0;
        $vlTotalMes12 = 0;
        $vlTotalTotal = 0;
        
        $inCountReceitas = 1;
        $arDemostrativoReceita = array();
        while( !$rsReceitas->eof() )
        {
            
            $arDemostrativoReceita[$inCountReceitas]["nom_conta"]     = $rsReceitas->getCampo("nom_conta");
            $arDemostrativoReceita[$inCountReceitas]["cod_estrutural"]= $rsReceitas->getCampo("cod_estrutural");
            $arDemostrativoReceita[$inCountReceitas]["mes_1"]         = number_format($rsReceitas->getCampo("mes_1") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_2"]         = number_format($rsReceitas->getCampo("mes_2") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_3"]         = number_format($rsReceitas->getCampo("mes_3") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_4"]         = number_format($rsReceitas->getCampo("mes_4") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_5"]         = number_format($rsReceitas->getCampo("mes_5") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_6"]         = number_format($rsReceitas->getCampo("mes_6") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_7"]         = number_format($rsReceitas->getCampo("mes_7") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_8"]         = number_format($rsReceitas->getCampo("mes_8") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_9"]         = number_format($rsReceitas->getCampo("mes_9") , 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_10"]        = number_format($rsReceitas->getCampo("mes_10"), 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_11"]        = number_format($rsReceitas->getCampo("mes_11"), 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["mes_12"]        = number_format($rsReceitas->getCampo("mes_12"), 2, ',','.');
            $arDemostrativoReceita[$inCountReceitas]["total"]         = number_format($rsReceitas->getCampo("total") , 2, ',','.');
            
            if ( $rsReceitas->getCampo("cod_estrutural") == '10000000' || $rsReceitas->getCampo("cod_estrutural") == '90000000' ) {
                $vlTotalMes1  = $vlTotalMes1  + $rsReceitas->getCampo("mes_1");
                $vlTotalMes2  = $vlTotalMes2  + $rsReceitas->getCampo("mes_2");
                $vlTotalMes3  = $vlTotalMes3  + $rsReceitas->getCampo("mes_3");
                $vlTotalMes4  = $vlTotalMes4  + $rsReceitas->getCampo("mes_4");
                $vlTotalMes5  = $vlTotalMes5  + $rsReceitas->getCampo("mes_5");
                $vlTotalMes6  = $vlTotalMes6  + $rsReceitas->getCampo("mes_6");
                $vlTotalMes7  = $vlTotalMes7  + $rsReceitas->getCampo("mes_7");
                $vlTotalMes8  = $vlTotalMes8  + $rsReceitas->getCampo("mes_8");
                $vlTotalMes9  = $vlTotalMes9  + $rsReceitas->getCampo("mes_9");
                $vlTotalMes10 = $vlTotalMes10 + $rsReceitas->getCampo("mes_10");
                $vlTotalMes11 = $vlTotalMes11 + $rsReceitas->getCampo("mes_11");
                $vlTotalMes12 = $vlTotalMes12 + $rsReceitas->getCampo("mes_12");
                $vlTotalTotal = $vlTotalTotal + $rsReceitas->getCampo("total");
            }
            $inCountReceitas++;
            
            $rsReceitas->proximo();
        }
        $inCountReceitas = 0;
        
        // MONTA TOTAIS POR MES E TOTAL DE RECEITAS
        $arDemostrativoReceitaTotal[$inCountReceitas]["nom_conta"] = "TOTAL RECEITAS";
        $arDemostrativoReceitaTotal[$inCountReceitas]["cod_estrutural"] = "";
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_1"]  = number_format($vlTotalMes1 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_2"]  = number_format($vlTotalMes2 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_3"]  = number_format($vlTotalMes3 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_4"]  = number_format($vlTotalMes4 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_5"]  = number_format($vlTotalMes5 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_6"]  = number_format($vlTotalMes6 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_7"]  = number_format($vlTotalMes7 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_8"]  = number_format($vlTotalMes8 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_9"]  = number_format($vlTotalMes9 , 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_10"] = number_format($vlTotalMes10, 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_11"] = number_format($vlTotalMes11, 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["mes_12"] = number_format($vlTotalMes12, 2, ',','.');
        $arDemostrativoReceitaTotal[$inCountReceitas]["total"]  = number_format($vlTotalTotal, 2, ',','.');
        
        // Inicio de receitas de exclusao
        $obTTCEMGRelatorioDemostrativoRCL->setDado( "tipo_despesa" , 2);
        $obTTCEMGRelatorioDemostrativoRCL->recuperaReceitasDemonstrativoRCL( $rsReceitasExclusoes ); 
                
        $vlRecetaExclusaoTotalMes1  = 0;
        $vlRecetaExclusaoTotalMes2  = 0;
        $vlRecetaExclusaoTotalMes3  = 0;
        $vlRecetaExclusaoTotalMes4  = 0;
        $vlRecetaExclusaoTotalMes5  = 0;
        $vlRecetaExclusaoTotalMes6  = 0;
        $vlRecetaExclusaoTotalMes7  = 0;
        $vlRecetaExclusaoTotalMes8  = 0;
        $vlRecetaExclusaoTotalMes9  = 0;
        $vlRecetaExclusaoTotalMes10 = 0;
        $vlRecetaExclusaoTotalMes11 = 0;
        $vlRecetaExclusaoTotalMes12 = 0;
        $vlRecetaExclusaoTotalTotal = 0;
        
        $inCountReceitasExclusao = 1;
        $arDemostrativoReceitaExclusao = array();
        while( !$rsReceitasExclusoes->eof() )
        {
            
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["nom_conta"]     = $rsReceitasExclusoes->getCampo("nom_conta");
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["cod_estrutural"]= $rsReceitasExclusoes->getCampo("cod_estrutural");
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_1"]         = number_format($rsReceitasExclusoes->getCampo("mes_1") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_2"]         = number_format($rsReceitasExclusoes->getCampo("mes_2") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_3"]         = number_format($rsReceitasExclusoes->getCampo("mes_3") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_4"]         = number_format($rsReceitasExclusoes->getCampo("mes_4") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_5"]         = number_format($rsReceitasExclusoes->getCampo("mes_5") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_6"]         = number_format($rsReceitasExclusoes->getCampo("mes_6") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_7"]         = number_format($rsReceitasExclusoes->getCampo("mes_7") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_8"]         = number_format($rsReceitasExclusoes->getCampo("mes_8") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_9"]         = number_format($rsReceitasExclusoes->getCampo("mes_9") , 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_10"]        = number_format($rsReceitasExclusoes->getCampo("mes_10"), 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_11"]        = number_format($rsReceitasExclusoes->getCampo("mes_11"), 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["mes_12"]        = number_format($rsReceitasExclusoes->getCampo("mes_12"), 2, ',','.');
            $arDemostrativoReceitaExclusao[$inCountReceitasExclusao]["total"]         = number_format($rsReceitasExclusoes->getCampo("total") , 2, ',','.');
            
            if ( $rsReceitasExclusoes->getCampo("cod_estrutural") == '10000000') {
                $vlRecetaExclusaoTotalMes1  = $vlRecetaExclusaoTotalMes1  + $rsReceitasExclusoes->getCampo("mes_1");
                $vlRecetaExclusaoTotalMes2  = $vlRecetaExclusaoTotalMes2  + $rsReceitasExclusoes->getCampo("mes_2");
                $vlRecetaExclusaoTotalMes3  = $vlRecetaExclusaoTotalMes3  + $rsReceitasExclusoes->getCampo("mes_3");
                $vlRecetaExclusaoTotalMes4  = $vlRecetaExclusaoTotalMes4  + $rsReceitasExclusoes->getCampo("mes_4");
                $vlRecetaExclusaoTotalMes5  = $vlRecetaExclusaoTotalMes5  + $rsReceitasExclusoes->getCampo("mes_5");
                $vlRecetaExclusaoTotalMes6  = $vlRecetaExclusaoTotalMes6  + $rsReceitasExclusoes->getCampo("mes_6");
                $vlRecetaExclusaoTotalMes7  = $vlRecetaExclusaoTotalMes7  + $rsReceitasExclusoes->getCampo("mes_7");
                $vlRecetaExclusaoTotalMes8  = $vlRecetaExclusaoTotalMes8  + $rsReceitasExclusoes->getCampo("mes_8");
                $vlRecetaExclusaoTotalMes9  = $vlRecetaExclusaoTotalMes9  + $rsReceitasExclusoes->getCampo("mes_9");
                $vlRecetaExclusaoTotalMes10 = $vlRecetaExclusaoTotalMes10 + $rsReceitasExclusoes->getCampo("mes_10");
                $vlRecetaExclusaoTotalMes11 = $vlRecetaExclusaoTotalMes11 + $rsReceitasExclusoes->getCampo("mes_11");
                $vlRecetaExclusaoTotalMes12 = $vlRecetaExclusaoTotalMes12 + $rsReceitasExclusoes->getCampo("mes_12");
                $vlRecetaExclusaoTotalTotal = $vlRecetaExclusaoTotalTotal + $rsReceitasExclusoes->getCampo("total");
            }
            $inCountReceitasExclusao++;
            
            $rsReceitasExclusoes->proximo();
        }
        $inCountReceitasExclusao = 0;
        // MONTA TOTAIS POR MES E TOTAL DE RECEITAS
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["nom_conta"] = "TOTAL RECEITAS";
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["cod_estrutural"] = "";
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_1"]  = number_format($vlRecetaExclusaoTotalMes1 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_2"]  = number_format($vlRecetaExclusaoTotalMes2 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_3"]  = number_format($vlRecetaExclusaoTotalMes3 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_4"]  = number_format($vlRecetaExclusaoTotalMes4 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_5"]  = number_format($vlRecetaExclusaoTotalMes5 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_6"]  = number_format($vlRecetaExclusaoTotalMes6 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_7"]  = number_format($vlRecetaExclusaoTotalMes7 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_8"]  = number_format($vlRecetaExclusaoTotalMes8 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_9"]  = number_format($vlRecetaExclusaoTotalMes9 , 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_10"] = number_format($vlRecetaExclusaoTotalMes10, 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_11"] = number_format($vlRecetaExclusaoTotalMes11, 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["mes_12"] = number_format($vlRecetaExclusaoTotalMes12, 2, ',','.');
        $arDemostrativoReceitaExclusaoTotal[$inCountReceitasExclusao]["total"]  = number_format($vlRecetaExclusaoTotalTotal, 2, ',','.');
  
        // Montando tabela de despesas.
        // Só vai trazer despesas sem as deduções 1.
        $obTTCEMGRelatorioDemostrativoRCL->setDado("tipo_despesa", 1);
        $obTTCEMGRelatorioDemostrativoRCL->recuperaDespesasDemonstrativoRCL( $rsDespesas );
        
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
            
            if ( $rsDespesas->getCampo("cod_estrutural") == '30000000' ) {
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
        $arDemostrativoDespesaTotal[$inCountDespesas]["nom_conta"] = "TOTAL DESPESAS COM PESSOAL";
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
        // Só vai trazer as deduções de despesa 2.

        $stFiltro = " WHERE cod_estrutural IN ('31900101', '31900301')";

        $obTTCEMGRelatorioDemostrativoRCL->setDado("tipo_despesa", 2);
        $obTTCEMGRelatorioDemostrativoRCL->recuperaDespesasDemonstrativoRCL( $rsDespesasDeducoes, $stFiltro );
        
        $vlTotalDespesasDeducoesMes1  = 0;
        $vlTotalDespesasDeducoesMes2  = 0;
        $vlTotalDespesasDeducoesMes3  = 0;
        $vlTotalDespesasDeducoesMes4  = 0;
        $vlTotalDespesasDeducoesMes5  = 0;
        $vlTotalDespesasDeducoesMes6  = 0;
        $vlTotalDespesasDeducoesMes7  = 0;
        $vlTotalDespesasDeducoesMes8  = 0;
        $vlTotalDespesasDeducoesMes9  = 0;
        $vlTotalDespesasDeducoesMes10 = 0;
        $vlTotalDespesasDeducoesMes11 = 0;
        $vlTotalDespesasDeducoesMes12 = 0;
        $vlTotalDespesasDeducoesTotal = 0;
        
        $inCountDespesasDeducoes = 1;
        $arDemostrativoDespesaDeducoes = array();
        while( !$rsDespesasDeducoes->eof() )
        {
            
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["nom_conta"]     = $rsDespesasDeducoes->getCampo("nom_conta");
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["cod_estrutural"]= $rsDespesasDeducoes->getCampo("cod_estrutural");
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_1"]         = number_format($rsDespesasDeducoes->getCampo("mes_1") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_2"]         = number_format($rsDespesasDeducoes->getCampo("mes_2") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_3"]         = number_format($rsDespesasDeducoes->getCampo("mes_3") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_4"]         = number_format($rsDespesasDeducoes->getCampo("mes_4") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_5"]         = number_format($rsDespesasDeducoes->getCampo("mes_5") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_6"]         = number_format($rsDespesasDeducoes->getCampo("mes_6") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_7"]         = number_format($rsDespesasDeducoes->getCampo("mes_7") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_8"]         = number_format($rsDespesasDeducoes->getCampo("mes_8") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_9"]         = number_format($rsDespesasDeducoes->getCampo("mes_9") , 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_10"]        = number_format($rsDespesasDeducoes->getCampo("mes_10"), 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_11"]        = number_format($rsDespesasDeducoes->getCampo("mes_11"), 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["mes_12"]        = number_format($rsDespesasDeducoes->getCampo("mes_12"), 2, ',','.');
            $arDemostrativoDespesaDeducoes[$inCountDespesasDeducoes]["total"]         = number_format($rsDespesasDeducoes->getCampo("total") , 2, ',','.');
            
            $vlTotalDespesasDeducoesMes1  = $vlTotalDespesasDeducoesMes1  + $rsDespesasDeducoes->getCampo("mes_1");
            $vlTotalDespesasDeducoesMes2  = $vlTotalDespesasDeducoesMes2  + $rsDespesasDeducoes->getCampo("mes_2");
            $vlTotalDespesasDeducoesMes3  = $vlTotalDespesasDeducoesMes3  + $rsDespesasDeducoes->getCampo("mes_3");
            $vlTotalDespesasDeducoesMes4  = $vlTotalDespesasDeducoesMes4  + $rsDespesasDeducoes->getCampo("mes_4");
            $vlTotalDespesasDeducoesMes5  = $vlTotalDespesasDeducoesMes5  + $rsDespesasDeducoes->getCampo("mes_5");
            $vlTotalDespesasDeducoesMes6  = $vlTotalDespesasDeducoesMes6  + $rsDespesasDeducoes->getCampo("mes_6");
            $vlTotalDespesasDeducoesMes7  = $vlTotalDespesasDeducoesMes7  + $rsDespesasDeducoes->getCampo("mes_7");
            $vlTotalDespesasDeducoesMes8  = $vlTotalDespesasDeducoesMes8  + $rsDespesasDeducoes->getCampo("mes_8");
            $vlTotalDespesasDeducoesMes9  = $vlTotalDespesasDeducoesMes9  + $rsDespesasDeducoes->getCampo("mes_9");
            $vlTotalDespesasDeducoesMes10 = $vlTotalDespesasDeducoesMes10 + $rsDespesasDeducoes->getCampo("mes_10");
            $vlTotalDespesasDeducoesMes11 = $vlTotalDespesasDeducoesMes11 + $rsDespesasDeducoes->getCampo("mes_11");
            $vlTotalDespesasDeducoesMes12 = $vlTotalDespesasDeducoesMes12 + $rsDespesasDeducoes->getCampo("mes_12");
            $vlTotalDespesasDeducoesTotal = $vlTotalDespesasDeducoesTotal + $rsDespesasDeducoes->getCampo("total");
            
            $inCountDespesasDeducoes++;
            
            $rsDespesasDeducoes->proximo();
        }
        $inCountDespesasDeducoes = 0;
        // MONTA TOTAIS POR MES E TOTAL DE DEDUÇÕES DE DESPESAS
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["nom_conta"] = "TOTAL DEDUÇÕES";
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["cod_estrutural"] = "";
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_1"]  = number_format($vlTotalDespesasDeducoesMes1 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_2"]  = number_format($vlTotalDespesasDeducoesMes2 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_3"]  = number_format($vlTotalDespesasDeducoesMes3 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_4"]  = number_format($vlTotalDespesasDeducoesMes4 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_5"]  = number_format($vlTotalDespesasDeducoesMes5 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_6"]  = number_format($vlTotalDespesasDeducoesMes6 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_7"]  = number_format($vlTotalDespesasDeducoesMes7 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_8"]  = number_format($vlTotalDespesasDeducoesMes8 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_9"]  = number_format($vlTotalDespesasDeducoesMes9 , 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_10"] = number_format($vlTotalDespesasDeducoesMes10, 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_11"] = number_format($vlTotalDespesasDeducoesMes11, 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["mes_12"] = number_format($vlTotalDespesasDeducoesMes12, 2, ',','.');
        $arDemostrativoDespesaDeducoesTotal[$inCountDespesasDeducoes]["total"]  = number_format($vlTotalDespesasDeducoesTotal, 2, ',','.');
        
        
        $vlTotaisDespesaMes1  = $vlTotalDespesasMes1  - $vlTotalDespesasDeducoesMes1;
        $vlTotaisDespesaMes2  = $vlTotalDespesasMes2  - $vlTotalDespesasDeducoesMes2;
        $vlTotaisDespesaMes3  = $vlTotalDespesasMes3  - $vlTotalDespesasDeducoesMes3;
        $vlTotaisDespesaMes4  = $vlTotalDespesasMes4  - $vlTotalDespesasDeducoesMes4;
        $vlTotaisDespesaMes5  = $vlTotalDespesasMes5  - $vlTotalDespesasDeducoesMes5;
        $vlTotaisDespesaMes6  = $vlTotalDespesasMes6  - $vlTotalDespesasDeducoesMes6;
        $vlTotaisDespesaMes7  = $vlTotalDespesasMes7  - $vlTotalDespesasDeducoesMes7;
        $vlTotaisDespesaMes8  = $vlTotalDespesasMes8  - $vlTotalDespesasDeducoesMes8;
        $vlTotaisDespesaMes9  = $vlTotalDespesasMes9  - $vlTotalDespesasDeducoesMes9;
        $vlTotaisDespesaMes10 = $vlTotalDespesasMes10 - $vlTotalDespesasDeducoesMes10;
        $vlTotaisDespesaMes11 = $vlTotalDespesasMes11 - $vlTotalDespesasDeducoesMes11;
        $vlTotaisDespesaMes12 = $vlTotalDespesasMes12 - $vlTotalDespesasDeducoesMes12;
        $vlTotaisDespesaTotal = $vlTotalDespesasTotal - $vlTotalDespesasDeducoesTotal;
              
        $arValorTotalDespesaPessoal[0]["nom_conta"]      = "TOTAL DESPESAS COM PESSOAL";
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
        
        $vlReceitaCorrenteLiquidaMes1  = $vlTotalMes1  - $vlRecetaExclusaoTotalMes1;
        $vlReceitaCorrenteLiquidaMes2  = $vlTotalMes2  - $vlRecetaExclusaoTotalMes2;
        $vlReceitaCorrenteLiquidaMes3  = $vlTotalMes3  - $vlRecetaExclusaoTotalMes3;
        $vlReceitaCorrenteLiquidaMes4  = $vlTotalMes4  - $vlRecetaExclusaoTotalMes4;
        $vlReceitaCorrenteLiquidaMes5  = $vlTotalMes5  - $vlRecetaExclusaoTotalMes5;
        $vlReceitaCorrenteLiquidaMes6  = $vlTotalMes6  - $vlRecetaExclusaoTotalMes6;
        $vlReceitaCorrenteLiquidaMes7  = $vlTotalMes7  - $vlRecetaExclusaoTotalMes7;
        $vlReceitaCorrenteLiquidaMes8  = $vlTotalMes8  - $vlRecetaExclusaoTotalMes8;
        $vlReceitaCorrenteLiquidaMes9  = $vlTotalMes9  - $vlRecetaExclusaoTotalMes9;
        $vlReceitaCorrenteLiquidaMes10 = $vlTotalMes10 - $vlRecetaExclusaoTotalMes10;
        $vlReceitaCorrenteLiquidaMes11 = $vlTotalMes11 - $vlRecetaExclusaoTotalMes11;
        $vlReceitaCorrenteLiquidaMes12 = $vlTotalMes12 - $vlRecetaExclusaoTotalMes12;
        $vlReceitaCorrenteLiquidaTotal = $vlTotalTotal - $vlRecetaExclusaoTotalTotal;
        
        
        // Montando valores finais do relatório RECEITA CORRENTE LIQUIDA
        $arValoresDemostrativoRCL = array();
        $arValoresDemostrativoRCL[0]["nom_conta"]      = "RECEITA CORRENTE LIQUIDA";
        $arValoresDemostrativoRCL[0]["cod_estrutural"] = "";
        $arValoresDemostrativoRCL[0]["mes_1"]  = number_format($vlReceitaCorrenteLiquidaMes1 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_2"]  = number_format($vlReceitaCorrenteLiquidaMes2 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_3"]  = number_format($vlReceitaCorrenteLiquidaMes3 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_4"]  = number_format($vlReceitaCorrenteLiquidaMes4 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_5"]  = number_format($vlReceitaCorrenteLiquidaMes5 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_6"]  = number_format($vlReceitaCorrenteLiquidaMes6 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_7"]  = number_format($vlReceitaCorrenteLiquidaMes7 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_8"]  = number_format($vlReceitaCorrenteLiquidaMes8 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_9"]  = number_format($vlReceitaCorrenteLiquidaMes9 , 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_10"] = number_format($vlReceitaCorrenteLiquidaMes10, 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_11"] = number_format($vlReceitaCorrenteLiquidaMes11, 2, ',','.');
        $arValoresDemostrativoRCL[0]["mes_12"] = number_format($vlReceitaCorrenteLiquidaMes12, 2, ',','.');
        $arValoresDemostrativoRCL[0]["total"]  = number_format($vlReceitaCorrenteLiquidaTotal, 2, ',','.');
        
        
        // Montando valores finais do relatório % APICAÇÃO
        $arValoresDemostrativoRCL[1]["nom_conta"]      = "% APICAÇÃO";
        $arValoresDemostrativoRCL[1]["cod_estrutural"] = "";
        $arValoresDemostrativoRCL[1]["mes_1"]  = number_format(((($vlTotaisDespesaMes1 * 100) / ($vlReceitaCorrenteLiquidaMes1  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes1  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_2"]  = number_format(((($vlTotaisDespesaMes2 * 100) / ($vlReceitaCorrenteLiquidaMes2  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes2  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_3"]  = number_format(((($vlTotaisDespesaMes3 * 100) / ($vlReceitaCorrenteLiquidaMes3  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes3  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_4"]  = number_format(((($vlTotaisDespesaMes4 * 100) / ($vlReceitaCorrenteLiquidaMes4  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes4  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_5"]  = number_format(((($vlTotaisDespesaMes5 * 100) / ($vlReceitaCorrenteLiquidaMes5  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes5  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_6"]  = number_format(((($vlTotaisDespesaMes6 * 100) / ($vlReceitaCorrenteLiquidaMes6  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes6  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_7"]  = number_format(((($vlTotaisDespesaMes7 * 100) / ($vlReceitaCorrenteLiquidaMes7  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes7  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_8"]  = number_format(((($vlTotaisDespesaMes8 * 100) / ($vlReceitaCorrenteLiquidaMes8  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes8  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_9"]  = number_format(((($vlTotaisDespesaMes9 * 100) / ($vlReceitaCorrenteLiquidaMes9  == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes9  ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_10"] = number_format(((($vlTotaisDespesaMes10* 100) / ($vlReceitaCorrenteLiquidaMes10 == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes10 ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_11"] = number_format(((($vlTotaisDespesaMes11* 100) / ($vlReceitaCorrenteLiquidaMes11 == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes11 ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["mes_12"] = number_format(((($vlTotaisDespesaMes12* 100) / ($vlReceitaCorrenteLiquidaMes12 == 0.00 ? 1 : $vlReceitaCorrenteLiquidaMes12 ))), 2, ',','.');
        $arValoresDemostrativoRCL[1]["total"]  = number_format(((($vlTotaisDespesaTotal* 100) / ($vlReceitaCorrenteLiquidaTotal == 0.00 ? 1 : $vlReceitaCorrenteLiquidaTotal ))), 2, ',','.');
               
        if($this->getTipoConsulta() == "PrefeituraInstituto"){
            $inPercentual = 54;
        }else{
            $inPercentual = 6;
        }
        
        $vlDefinidoPorLei54Mes1  = ($inPercentual * $vlReceitaCorrenteLiquidaMes1 ) / 100;
        $vlDefinidoPorLei54Mes2  = ($inPercentual * $vlReceitaCorrenteLiquidaMes2 ) / 100;
        $vlDefinidoPorLei54Mes3  = ($inPercentual * $vlReceitaCorrenteLiquidaMes3 ) / 100;
        $vlDefinidoPorLei54Mes4  = ($inPercentual * $vlReceitaCorrenteLiquidaMes4 ) / 100;
        $vlDefinidoPorLei54Mes5  = ($inPercentual * $vlReceitaCorrenteLiquidaMes5 ) / 100;
        $vlDefinidoPorLei54Mes6  = ($inPercentual * $vlReceitaCorrenteLiquidaMes6 ) / 100;
        $vlDefinidoPorLei54Mes7  = ($inPercentual * $vlReceitaCorrenteLiquidaMes7 ) / 100;
        $vlDefinidoPorLei54Mes8  = ($inPercentual * $vlReceitaCorrenteLiquidaMes8 ) / 100;
        $vlDefinidoPorLei54Mes9  = ($inPercentual * $vlReceitaCorrenteLiquidaMes9 ) / 100;
        $vlDefinidoPorLei54Mes10 = ($inPercentual * $vlReceitaCorrenteLiquidaMes10) / 100;
        $vlDefinidoPorLei54Mes11 = ($inPercentual * $vlReceitaCorrenteLiquidaMes11) / 100;
        $vlDefinidoPorLei54Mes12 = ($inPercentual * $vlReceitaCorrenteLiquidaMes12) / 100;
        $vlDefinidoPorLei54Total = ($inPercentual * $vlReceitaCorrenteLiquidaTotal) / 100;

        
        // Montando valores finais do relatório % DEFINIDO POR LEI 54 %
        $arValoresDemostrativoRCL[2]["nom_conta"]      = "% DEFINIDO POR LEI ".$inPercentual."%";
        $arValoresDemostrativoRCL[2]["cod_estrutural"] = "";
        $arValoresDemostrativoRCL[2]["mes_1"]  = number_format($vlDefinidoPorLei54Mes1 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_2"]  = number_format($vlDefinidoPorLei54Mes2 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_3"]  = number_format($vlDefinidoPorLei54Mes3 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_4"]  = number_format($vlDefinidoPorLei54Mes4 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_5"]  = number_format($vlDefinidoPorLei54Mes5 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_6"]  = number_format($vlDefinidoPorLei54Mes6 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_7"]  = number_format($vlDefinidoPorLei54Mes7 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_8"]  = number_format($vlDefinidoPorLei54Mes8 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_9"]  = number_format($vlDefinidoPorLei54Mes9 , 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_10"] = number_format($vlDefinidoPorLei54Mes10, 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_11"] = number_format($vlDefinidoPorLei54Mes11, 2, ',','.');
        $arValoresDemostrativoRCL[2]["mes_12"] = number_format($vlDefinidoPorLei54Mes12, 2, ',','.');
        $arValoresDemostrativoRCL[2]["total"]  = number_format($vlDefinidoPorLei54Total, 2, ',','.');
        
                
        // Montando valores finais do relatório 95 % S/54% DEFINIDO POR LEI
        $arValoresDemostrativoRCL[3]["nom_conta"]      = "95% S/".$inPercentual."% DEFINIDO POR LEI";
        $arValoresDemostrativoRCL[3]["cod_estrutural"] = "";
        $arValoresDemostrativoRCL[3]["mes_1"]  = number_format((($vlDefinidoPorLei54Mes1 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_2"]  = number_format((($vlDefinidoPorLei54Mes2 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_3"]  = number_format((($vlDefinidoPorLei54Mes3 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_4"]  = number_format((($vlDefinidoPorLei54Mes4 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_5"]  = number_format((($vlDefinidoPorLei54Mes5 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_6"]  = number_format((($vlDefinidoPorLei54Mes6 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_7"]  = number_format((($vlDefinidoPorLei54Mes7 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_8"]  = number_format((($vlDefinidoPorLei54Mes8 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_9"]  = number_format((($vlDefinidoPorLei54Mes9 * 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_10"] = number_format((($vlDefinidoPorLei54Mes10* 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_11"] = number_format((($vlDefinidoPorLei54Mes11* 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["mes_12"] = number_format((($vlDefinidoPorLei54Mes12* 95) / 100), 2, ',','.');
        $arValoresDemostrativoRCL[3]["total"]  = number_format((($vlDefinidoPorLei54Total* 95) / 100), 2, ',','.');
        
        
        $rsRecordSet["arReceitas"]                    = $arDemostrativoReceita;
        $rsRecordSet["arReceitasTotal"]               = $arDemostrativoReceitaTotal;
        $rsRecordSet["arDemostrativoReceitaExclusao"] = $arDemostrativoReceitaExclusao;
        $rsRecordSet["arDespesas"]                    = $arDemostrativoDespesa;
        $rsRecordSet["arDespesasTotal"]               = $arDemostrativoDespesaTotal;
        $rsRecordSet["arDespesasDeducoes"]            = $arDemostrativoDespesaDeducoes;
        $rsRecordSet["arDespesasDeducoesTotal"]       = $arDemostrativoDespesaDeducoesTotal;
        $rsRecordSet["arValorTotalDespesaPessoal"]    = $arValorTotalDespesaPessoal;
        $rsRecordSet["arValoresDemostrativoRCL"]      = $arValoresDemostrativoRCL;
    }
}
?>