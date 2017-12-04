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
 * PÃ¡gina de Oculto - Seta parÃ¢metros para gerar relatÃ³rio
 * Data de CriaÃ§Ã£o   : 26/11/2008

 * @author Analista      Sabrina Moreira
 * @author Desenvolvedor Alexandre Melo

 * @package URBEM
 * @subpackage

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(5,25,5);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório de Certidões Emitidas');
$preview->addParametro( 'num_documento'     , $_POST['inNumDocumento'] 	);
$preview->addParametro( 'numcgm'            , $_POST['inCGM'] 			);
$preview->addParametro( 'data_inicial'      , $_POST['stDataInicial'] 	);
$preview->addParametro( 'data_final'        , $_POST['stDataFinal'] 	);
$preview->addParametro( 'cod_tipo_documento', $_POST['stTipoDocumento'] );
$preview->setFormato('html');
$preview->preview();

?>
