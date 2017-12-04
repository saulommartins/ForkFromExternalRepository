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
/*
 * Página de Oculto - Seta parâmetros para gerar relatório
 * Data de Criação   : 26/11/2008

 * @author Analista      Sabrina Moreira
 * @author Desenvolvedor Alexandre Melo

 * @package URBEM
 * @subpackage

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

    //Localização
    if ($_REQUEST['inCodInicioLocalizacao'] != "" and $_REQUEST['inCodTerminoLocalizacao'] != "") {
        $stFiltro .= " AND localizacao.codigo_composto BETWEEN '".$_REQUEST['inCodInicioLocalizacao'].
                                                     "' AND '".$_REQUEST['inCodTerminoLocalizacao']."'";
    } else {
        if ($_REQUEST['inCodInicioLocalizacao'] != "") {
            $stFiltro .= " AND localizacao.codigo_composto = '".$_REQUEST['inCodInicioLocalizacao']."'";
        }
        if ($_REQUEST['inCodTerminoLocalizacao'] != "") {
            $stFiltro .= " AND localizacao.codigo_composto = ".$_REQUEST['inCodTerminoLocalizacao']."'";
        }
    }

    //Lote
    if ($_REQUEST['inCodLoteInicio'] != "" and $_REQUEST['inCodLoteFinal'] != "") {
        $stFiltro .= " AND lote_localizacao.valor BETWEEN '".$_REQUEST['inCodLoteInicio'].
                                                "' AND '".$_REQUEST['inCodLoteFinal']."'";
    } else {
        if ($_REQUEST['inCodLoteInicio'] != "") {
            $stFiltro .= " AND lote_localizacao.valor = '".$_REQUEST['inCodLoteInicio']."'";
        }
        if ($_REQUEST['inCodLoteFinal'] != "") {
            $stFiltro .= " AND lote_localizacao.valor = '".$_REQUEST['inCodLoteFinal']."'";
        }
    }

    //Inscrição Imobiliária
    if ($_REQUEST['inNumInscricaoImobiliariaInicial'] != "" and $_REQUEST['inNumInscricaoImobiliariaFinal'] != "") {
        $stFiltro .= " AND imovel_lote.inscricao_municipal BETWEEN ".$_REQUEST['inNumInscricaoImobiliariaInicial'].
                                                         " AND ".$_REQUEST['inNumInscricaoImobiliariaFinal'];
    } else {
        if ($_REQUEST['inNumInscricaoImobiliariaInicial'] != "") {
            $stFiltro .= " AND imovel_lote.inscricao_municipal = ".$_REQUEST['inNumInscricaoImobiliariaInicial'];
        }
        if ($_REQUEST['inNumInscricaoImobiliariaFinal'] != "") {
            $stFiltro .= " AND imovel_lote.inscricao_municipal = ".$_REQUEST['inNumInscricaoImobiliariaFinal'];
        }
    }

    //Ordenação
    if ($_REQUEST['stOrder'] != "") {
        $stFiltro .= " ORDER BY ".$_REQUEST['stOrder'];
    }

    $preview = new PreviewBirt(5,25,3);
    $preview->setVersaoBirt('2.5.0');
    $preview->setTitulo('Relatório de Valores Venais');
    $preview->addParametro( 'stFiltro', $stFiltro );
    $preview->addParametro( 'exercicio', Sessao::getExercicio());
    $preview->setFormato('pdf');
    $preview->preview();

?>
