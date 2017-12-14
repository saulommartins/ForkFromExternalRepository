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
    * PÃ¡gina de GeraÃ§Ã£o para RelatÃ³rio de Pagamentos Extras
    * Data de CriaÃ§Ã£o   : 10/08/2007
    *
    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar RÃ©gis Bernardo

    * @ignore

    $Revision: 31732 $
    $Name$
    $Author: tonismar $
    $Date: 2007-08-30 16:18:25 -0300 (Qui, 30 Ago 2007) $

    * Casos de uso: uc-02.04.38

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(2,30,4);
$preview->setTitulo('Relatório do Birt');
$preview->setNomeArquivo('relacaoPagamentosExtras');

// parametro para filtro de entidade
$preview->addParametro('entidade'    , implode(',',$_REQUEST['inCodEntidade']) );

// parametros para periodicidade
$preview->addParametro('initial_date', $_REQUEST['stDataInicial']     );
$preview->addParametro('final_date'  , $_REQUEST['stDataFinal']       );

// parametros para conta caixa
if ($_REQUEST['inCodPlanoCredito']) {
    $preview->addParametro('ccaixa'      , $_REQUEST['inCodPlanoCredito'] );
    $preview->addParametro('f_ccaixa'    , $_REQUEST['inCodPlanoCredito']." - ".$_REQUEST['stNomContaCredito'] );
} else {
    $preview->addParametro('ccaixa'      , "" );
    $preview->addParametro('f_ccaixa'    , "" );
}

// parametros para conta debito
if ($_REQUEST['inCodPlanoDebito']) {
    $preview->addParametro('cdespesa'    , $_REQUEST['inCodPlanoDebito']  );
    $preview->addParametro('f_cdespesa'  , $_REQUEST['inCodPlanoDebito']." - ".$_REQUEST['stNomContaDebito']  );
} else {
    $preview->addParametro('cdespesa'    , ""  );
    $preview->addParametro('f_cdespesa'  , ""  );
}

$preview->preview();
?>
