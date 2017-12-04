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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 22/03/2016

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM

    $Id: OCRelatorioEmpenhoModalidade.php 64778 2016-03-31 13:51:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($request->get('stCtrl')) {
    default:
        include_once '../../../../../../config.php';
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
        include_once CLA_MPDF;
        include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
        include_once CAM_FW_PDF."RRelatorio.class.php";

        $arFiltro = Sessao::read('filtroRelatorio');
        $request = new Request($arFiltro);

        foreach ($request->getAll() as $key=>$value) {
            if ( strstr( $key, 'atributos_' ) ) {
                $arModalidade = explode('-', $value);

                $request->set('cod_modalidade', $arModalidade[0]);
                $request->set('nom_modalidade', $arModalidade[1]);
            }
        }

        $stCodEntidades = implode(', ', $request->get('inCodEntidade'));

        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $obTEmpenhoEmpenho->setDado('exercicio', Sessao::getExercicio());
        $obTEmpenhoEmpenho->setDado('entidade', $stCodEntidades);

        $stFiltro = "";
        if($request->get('cod_modalidade', '') != ''){ 
            $stFiltro = " AND empenho.cod_modalidade = ".$request->get('cod_modalidade');
        }

        $obTEmpenhoEmpenho->recuperaEmpenhosPorModalidade($rsEmpenhoModalidade, $stFiltro);

        #AGRUPA PAGAMENTOS POR EMPENHO, TOTAL POR EXERCÍCIO E TOTAL GERAL
        $stModalidade = $request->get('nom_modalidade');
        $arEmpenho = array();
        $arInfoEmpenho = array();

        foreach($rsEmpenhoModalidade->getElementos() as $pagamento) {
            $stModalidade = $pagamento['modalidade'];
            $stCodEmpenho = $pagamento['cod_entidade'].'.'.$pagamento['exercicio'].'.'.$pagamento['cod_empenho'];

            $arEmpenho[$stCodEmpenho][] = $pagamento;

            $arInfoEmpenho[$stCodEmpenho]['empenho']       = $pagamento['cod_empenho']."/".$pagamento['exercicio'];
            $arInfoEmpenho[$stCodEmpenho]['exercicio']     = $pagamento['exercicio'];
            $arInfoEmpenho[$stCodEmpenho]['mes_empenho']   = $pagamento['mes_empenho'];
            $arInfoEmpenho[$stCodEmpenho]['dt_empenho']    = $pagamento['dt_empenho'];
            $arInfoEmpenho[$stCodEmpenho]['cod_entidade']  = $pagamento['cod_entidade'];
            $arInfoEmpenho[$stCodEmpenho]['entidade']      = $pagamento['entidade'];
            $arInfoEmpenho[$stCodEmpenho]['cgm_credor']    = $pagamento['cgm_credor'];
            $arInfoEmpenho[$stCodEmpenho]['credor']        = $pagamento['credor'];
            $arInfoEmpenho[$stCodEmpenho]['descricao']     = $pagamento['descricao'];
            $arInfoEmpenho[$stCodEmpenho]['vl_empenho']    = $pagamento['vl_empenho'];
            $arInfoEmpenho[$stCodEmpenho]['vl_liquidado'] += $pagamento['vl_nota'];
            $arInfoEmpenho[$stCodEmpenho]['vl_pago']      += $pagamento['vl_pagamento'];
        }

        $arTotalExercicio = array();
        $arTotal = array();
        foreach($arInfoEmpenho as $empenho) {                
            $arTotalExercicio[$empenho['exercicio']]['exercicio']     = $empenho['exercicio'];
            $arTotalExercicio[$empenho['exercicio']]['vl_empenho']   += $empenho['vl_empenho'];
            $arTotalExercicio[$empenho['exercicio']]['vl_liquidado'] += $empenho['vl_liquidado'];
            $arTotalExercicio[$empenho['exercicio']]['vl_pago']      += $empenho['vl_pago'];

            $arTotal['vl_empenho']   += $empenho['vl_empenho'];
            $arTotal['vl_liquidado'] += $empenho['vl_liquidado'];
            $arTotal['vl_pago']      += $empenho['vl_pago'];
        }

        //Array Filtro
        $inCount = 0;
        $arFiltro = array();
        $arFiltro[$inCount]['titulo'] = 'Exercício';
        $arFiltro[$inCount]['valor']  = Sessao::getExercicio();
        $inCount++;

        $arFiltro[$inCount]['titulo'] = 'Entidades';
        $arFiltro[$inCount]['valor']  = $stCodEntidades;

        $inCount++;

        $arFiltro[$inCount]['titulo'] = 'Data Inicial';
        $arFiltro[$inCount]['valor']  = $request->get('stDataInicial');

        $inCount++;

        $arFiltro[$inCount]['titulo'] = 'Data Final';
        $arFiltro[$inCount]['valor']  = $request->get('stDataFinal');

        if($request->get('cod_modalidade', '') != ''){
            $inCount++;

            $arFiltro[$inCount]['titulo'] = 'Modalidade';
            $arFiltro[$inCount]['valor']  = $request->get('cod_modalidade')." - ".$request->get('nom_modalidade');
        }

        //Array Assinaturas
        $assinaturas = Sessao::read('assinaturas');
        if ( count($assinaturas['selecionadas']) > 0 ) {
            include_once CAM_FW_PDF."RAssinaturas.class.php";
            $obRAssinaturas = new RAssinaturas;
            $obRAssinaturas->setArAssinaturas( $assinaturas['selecionadas'] );
            $rsAssinaturas = $obRAssinaturas->getArAssinaturas();

            foreach ($rsAssinaturas as $key => $assinatura) {
                $arAssinaturas[] = $rsAssinaturas[$key]->getElementos();
            }
        }

        $arDados['stModalidade']        = $stModalidade;
        $arDados['arEmpenho']           = $arEmpenho;
        $arDados['arInfoEmpenho']       = $arInfoEmpenho;
        $arDados['arTotalExercicio']    = $arTotalExercicio;
        $arDados['arTotal']             = $arTotal;
        $arDados['inCodEntidade']       = $stCodEntidades;
        $arDados['stDataInicial']       = $request->get('stDataInicial');
        $arDados['stDataFinal']         = $request->get('stDataFinal');

        if($arAssinaturas)
            $arDados['arAssinaturas']        = $arAssinaturas;
        $arDados['filtro']                   = $arFiltro;

        Sessao::write('arDados', $arDados);

        $obRRelatorio = new RRelatorio;
        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmpenhoModalidade.php" );
    break;
}

?>
