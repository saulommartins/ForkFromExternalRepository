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
    * Arquivo que monta o combo do modelo de documento
    * Data de Criação: 11/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-01.03.100
                    uc-03.05.15

*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($_REQUEST['stCtrl']) {
    case 'buscaTipo':
        if ($_REQUEST['stCodAcao'] && $_REQUEST['stCodDocumento']) {
            include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );
            $obModeloDocumento = new TAdministracaoModeloDocumento();
            $stFiltro = "where a.cod_acao = '".$_REQUEST['stCodAcao']."'";
            $stFiltro.= " and b.cod_documento = ".$_REQUEST['stCodDocumento'];
            $obModeloDocumento->recuperaRelacionamento( $rsDocumento, $stFiltro );
            if ( $rsDocumento->getNumLinhas() > 0 ) {
                $stJs = "f.inCodTipoDocumento.value = ".$rsDocumento->getCampo('cod_tipo_documento').";\n";
            } else {
                $stJs = "f.inCodTipoDocumento.value = '';\n";
            }
        }
    break;
}

echo $stJs;
?>
