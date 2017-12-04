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

    * Página de Relatório de Demonstrativo de Gastos com Pessoal
    * Data de Criação   : 09/07/2014
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @ignore
    *   
    * $Id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioAnexoIIIA.class.php");

include_once CLA_MPDF;

$obMPDF = new FrameWorkMPDF(6,55,9);
$obMPDF->setCodEntidades($request->get('inCodEntidade'));
$obMPDF->setDataInicio($request->get("stDataInicial"));
$obMPDF->setDataFinal($request->get("stDataFinal"));

$obMPDF->setNomeRelatorio("Anexo III A");

$obTTCEMGRelatorioAnexoIIIA = new TTCEMGRelatorioAnexoIIIA();
$obTTCEMGRelatorioAnexoIIIA->setDado('exercicio'    , Sessao::getExercicio());
$obTTCEMGRelatorioAnexoIIIA->setDado('dtInicial'    , $_REQUEST['stDataInicial']);
$obTTCEMGRelatorioAnexoIIIA->setDado('dtFinal'      , $_REQUEST['stDataFinal']);
$obTTCEMGRelatorioAnexoIIIA->setDado('cod_conta'    , implode(',',$_REQUEST['inCodContaSelecionados']));

$obTTCEMGRelatorioAnexoIIIA->recuperaDadosAnexoIIIA($rsRecordSet);

if ($rsRecordSet->getNumLinhas() >= 1){
    foreach ($rsRecordSet->getElementos() as $value) {
        switch ($value['nivel']) {
            case 1:
                $arAux['nivel']             = 1;
                $arAux['cod_funcao']        = $value['cod_funcao'];
                $arAux['cod_subfuncao']     = '';
                $arAux['cod_programa']      = '';
                $arAux['descricao']         = $value['descricao'];
                $arAux['valor_pagamento']   = number_format($value['valor_pagamento'],2,',','.');            
                $arDados["arReceitas"][]    = $arAux;
            break;
            
            case 2:
                $arAux['nivel']             = 2;
                $arAux['cod_funcao']        = '';
                $arAux['cod_subfuncao']     = $value['cod_subfuncao'];
                $arAux['cod_programa']      = '';
                $arAux['descricao']         = $value['descricao'];
                $arAux['valor_pagamento']   = number_format($value['valor_pagamento'],2,',','.');
                $arDados["arReceitas"][]    = $arAux;
            break;
            
            case 4:
                $arAux['nivel']             = 4;
                $arAux['cod_funcao']        = '';
                $arAux['cod_subfuncao']     = '';
                $arAux['cod_programa']      = $value['cod_programa'];
                $arAux['descricao']         = $value['descricao'];
                $arAux['valor_pagamento']   = number_format($value['valor_pagamento'],2,',','.');
                $arDados["arReceitas"][]    = $arAux;
            break;
        }
    }
} else {
    $arDados['sem_registro'] = 'Não existem registros!';
}

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();

?>