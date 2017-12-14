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
    * Página de Geração de Relatório do Calculo de 13 Salário
    * Data de Criação: 11/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-26 17:12:04 -0300 (Ter, 26 Jun 2007) $

    * Casos de uso: uc-04.05.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                        );
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                           );

$obRRelatorio           = new RRelatorio;
$obCGM                  = new RCGMPessoaFisica;
$obPDF                  = new ListaPDF();

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->setCodigoEntidade( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Pessoal" );
$obPDF->setTitulo            ( "Relatório de Erros do Cálculo da Folha 13º Salário" );
$obPDF->setSubTitulo         ( Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsRecordSet = Sessao::read("rsErros") ;
while ( !$rsRecordSet->eof() ) {
    $rsContrato = new Recordset;
    $rsContrato->preenche($rsRecordSet->getCampo('contrato'));
    $obPDF->addRecordSet($rsContrato);
    $obPDF->setAlinhamento  ( "R"           );
    $obPDF->addCabecalho    ( "",     10, 10);
    $obPDF->addCabecalho    ( "",     10, 10);
    $obPDF->setAlinhamento  ( "R"           );
    $obPDF->addCampo        ( "campo1", 8   );
    $obPDF->setAlinhamento  ( "L"           );
    $obPDF->addCampo        ( "campo2", 8   );

    $rsLogErro = new Recordset;
    $rsLogErro->preenche($rsRecordSet->getCampo('erros'));
    $obPDF->addRecordSet($rsLogErro);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento  ( "C"                        );
    $obPDF->addCabecalho    ( "Evento",          10, 10  );
    $obPDF->addCabecalho    ( "Descrição",       40, 10  );
    $obPDF->addCabecalho    ( "Erro do Cálculo", 40, 10  );
    $obPDF->setAlinhamento  ( "R"                        );
    $obPDF->addCampo        ( "evento", 8                );
    $obPDF->setAlinhamento  ( "L"                        );
    $obPDF->addCampo        ( "descricao", 8             );
    $obPDF->setAlinhamento  ( "L"                        );
    $obPDF->addCampo        ( "erro", 8                  );

    $rsRecordSet->proximo();
}

$obPDF->show();

?>
