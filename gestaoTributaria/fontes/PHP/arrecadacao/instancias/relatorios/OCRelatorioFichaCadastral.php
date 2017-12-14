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
    * Página de filtro do relatório
    * Data de Criação   : 16/10/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Vitor Hugo
    * @ignore

    * $Id: OCRelatorioFichaCadastral.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.10
*/

/*
$Log: ,v $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

    $stFiltro = ' WHERE ';

// CREDITO.ESPECIE.GENERO.NATUREZA
    $arCreditoInicio = explode('.', $_REQUEST['inCodCreditoInicio'] );
    $arCreditoFinal  = explode('.', $_REQUEST['inCodCreditoTermino']);
// Periodo
    $arDtInicial     = explode('/', $_REQUEST['dtInicio'] );
    $arDtFinal       = explode('/', $_REQUEST['dtFinal'] );
    $dtInicial       = $arDtInicial[1].'/'.$arDtInicial[0].'/'.$arDtInicial[2];
    $dtFinal         = $arDtFinal[1]  .'/'.$arDtFinal[0]  .'/'.$arDtFinal[2]  ;
//Grupo Credito
    $arGpCreditoIni  = explode('/', $_REQUEST['inCodGrupoInicio' ]);
    $arGpCreditoFin  = explode('/', $_REQUEST['inCodGrupoTermino']);

    if ($_REQUEST['inCodGrupoInicio'] && $_REQUEST['inCodGrupoTermino']) {
        $stFiltro .= " acgc.cod_grupo between "    .$arGpCreditoIni[0]." AND ".$arGpCreditoFin[0]." AND ";
        $stFiltro .= " acgc.ano_exercicio::integer between ".$arGpCreditoFin[1]." AND ".$arGpCreditoFin[1]." AND ";

    } elseif ($_REQUEST['inCodGrupoInicio']) {
        $stFiltro .= " acgc.cod_grupo     = ".$arGpCreditoIni[0]." AND ";
        $stFiltro .= " acgc.ano_exercicio::integer = ".$arGpCreditoIni[1]." AND ";

    } elseif ($_REQUEST['inCodCreditoInicio'] && $_REQUEST['inCodCreditoTermino']) {
        $stFiltro .= " acgc.cod_grupo IS NULL AND
                       calculo.cod_credito between ".$arCreditoInicio[0]." AND ".$arCreditoFinal[0]." AND
                       calculo.cod_especie between ".$arCreditoInicio[1]." AND ".$arCreditoFinal[1]." AND
                       calculo.cod_genero  between ".$arCreditoInicio[2]." AND ".$arCreditoFinal[2]." AND
                       calculo.cod_natureza between ".$arCreditoInicio[3]." AND ".$arCreditoFinal[3]." AND ";

    } elseif ($_REQUEST['inCodCreditoInicio']) {
        $stFiltro .= " acgc.cod_grupo IS NULL AND
                       calculo.cod_credito  = ".$arCreditoInicio[0]." AND
                       calculo.cod_especie  = ".$arCreditoInicio[1]." AND
                       calculo.cod_genero   = ".$arCreditoInicio[2]." AND
                       calculo.cod_natureza = ".$arCreditoInicio[3]." AND ";
    }

//Contribuinte
    if ($_REQUEST['inCodContribuinteInicial'] && $_REQUEST['inCodContribuinteFinal']) {
        $stFiltro .= ' calculo_cgm.numcgm between '.$_REQUEST['inCodContribuinteInicial'].' AND '.
                     $_REQUEST['inCodContribuinteFinal'].' AND ';

     } elseif ($_REQUEST['inCodContribuinteInicial']) {
        $stFiltro .= ' calculo_cgm.numcgm = '.$_REQUEST['inCodContribuinteInicial'].' AND ';
     }

//PERIODO
    if ($_REQUEST['dtInicio'] && $_REQUEST['dtFinal']) {
        $stFiltro .= " alc.dt_lancamento between '".$dtInicial."' AND '".$dtFinal."' AND ";
    } elseif ($_REQUEST['dtInicio']) {
        $stFiltro .= " alc.dt_lancamento = '".$dtInicial."' AND ";
    }

     $stFiltro = substr( $stFiltro, 0, strlen($stFiltro)-4);
    $preview = new PreviewBirt(5,25,1);
    $preview->setVersaoBirt('2.5.0');
    $preview->setTitulo('Relatório de Ficha Cadastral');
    $preview->addParametro( 'stFiltro', $stFiltro );
    $preview->setFormato('pdf');
    $preview->preview();
?>
