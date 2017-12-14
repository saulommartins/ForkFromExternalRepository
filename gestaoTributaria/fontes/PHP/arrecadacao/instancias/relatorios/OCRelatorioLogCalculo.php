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

    * $Id: OCRelatorioFichaCadastral.php 33057 2008-09-04 20:08:52Z andrem $

    * Casos de uso: uc-05.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$stFiltro = ' WHERE 1=1 ';

if ($_REQUEST['inCodGrupo']) {
    $arGrupo = explode('/', $_REQUEST['inCodGrupo']);
    $stFiltro .= " AND calculo_grupo_credito.cod_grupo = ".$arGrupo[0]." AND calculo_grupo_credito.ano_exercicio = '".$arGrupo[1]."'";
}

if ($_REQUEST['inNumInscricaoEconomicaInicial'] && $_REQUEST['inNumInscricaoEconomicaFinal']) {
    $stFiltro .= ' AND cec.inscricao_economica between '.$_REQUEST['inNumInscricaoEconomicaInicial'].' AND '.$_REQUEST['inNumInscricaoEconomicaFinal'];
} elseif ($_REQUEST['inNumInscricaoEconomicaInicial']) {
    $stFiltro .= ' AND cec.inscricao_economica = '.$_REQUEST['inNumInscricaoEconomicaInicial'].'';
}

if ($_REQUEST['inCodImovelInicial'] && $_REQUEST['inCodImovelFinal']) {
    $stFiltro .= ' AND imovel_calculo.inscricao_municipal between '.$_REQUEST['inCodImovelInicial'].' AND '. $_REQUEST['inCodImovelFinal'];
} elseif ($_REQUEST['inCodImovelInicial']) {
    $stFiltro .= ' AND imovel_calculo.inscricao_municipal = '.$_REQUEST['inCodImovelInicial'];
}

if (isset($_REQUEST['stSituacao']) && !empty($_REQUEST['stSituacao'])) {
    if ($_REQUEST['stSituacao'] == 'E') {
        $stFiltroAlc .= " WHERE LOWER(alc.valor) <> 'ok' ";
    } elseif ($_REQUEST['stSituacao'] == 'C') {
        $stFiltroAlc .= " WHERE POSITION('ok' in LOWER(alc.valor)) > 0 ";
    }
}

$preview = new PreviewBirt(5,25,2);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório de Log de Cálculo');
$preview->addParametro( 'stFiltro'    , $stFiltro );
$preview->addParametro( 'stFiltroAlc' , $stFiltroAlc );
$preview->setFormato('pdf');
$preview->preview();
    
?>
