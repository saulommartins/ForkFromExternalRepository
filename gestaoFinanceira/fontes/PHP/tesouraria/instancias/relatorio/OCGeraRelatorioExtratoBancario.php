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
    * Data de Criação   : 11/11/2004

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor:$
    $Date: 2008-01-15 10:58:06 -0200 (Ter, 15 Jan 2008) $

    * Casos de uso: uc-02.04.10
*/

/*
$Log$
Revision 1.21  2007/10/03 21:25:24  cako
Ticket#10254#

Revision 1.20  2007/09/14 21:36:08  cako
Ticket#10037#

Revision 1.19  2007/07/04 18:11:31  leandro.zis
Bug #9362#

Revision 1.18  2007/06/20 20:40:12  hboaventura
Bug#9103#

Revision 1.17  2007/06/13 13:35:21  bruce
Bug #9103#

Revision 1.16  2007/05/30 19:25:14  bruce
Bug #9116#

Revision 1.15  2006/11/14 19:04:46  cako
Bug #7233#

Revision 1.14  2006/07/05 20:39:48  cleisson
Adicionada tag Log aos arquivos
*/
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

$arDados  = Sessao::read('arDados');
$arFiltro = Sessao::read('filtroRelatorio');
$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF(  );

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodigoEntidadesSelecionadas'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodigoEntidadesSelecionadas'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}
$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Dados para Extrato de Conta" );
$obPDF->setSubTitulo         ( $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obROrcamentoEntidade = new ROrcamentoEntidade();
$obROrcamentoEntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obROrcamentoEntidade->listarUsuariosEntidade($rsEntidades , ' ORDER BY cod_entidade');

while (!$rsEntidades->eof()) {
    foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $inCodEntidade) {
        if ($inCodEntidade == $rsEntidades->getCampo('cod_entidade')) {
            $arNomEntidade[] = $rsEntidades->getCampo('nom_cgm');
        }
    }
    $rsEntidades->proximo();
}

$obPDF->addFiltro( 'Entidades Relacionadas ' , $arNomEntidade );

$i = 0;

foreach ($arDados[0] as $arContas) {
    $arNomeConta = array();
    $arNomeConta[0]['dados_banco'] = $arContas['dados_banco'];
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arNomeConta );

    $obPDF->addRecordSet( $rsRecordSet );
    if($i > 0) $obPDF->setQuebraPaginaLista( $arFiltro['stQuebraPagPorConta'] == 'sim'  );
    $i++;
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 100, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("dados_banco", 8 );

    $rsRecordSetMov = new RecordSet;
    if ( !is_array( $arContas['movimentacao'] ) ) {
        $arContas['movimentacao'] = array();
    }
    $rsRecordSetMov->preenche( $arContas['movimentacao'] );

    $obPDF->addRecordSet($rsRecordSetMov);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Data Movim.', 15, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Histórico', 50, 10);
    $obPDF->setAlinhamento ('R');
    $obPDF->addCabecalho   ("Valor R\$",20, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Saldo', 20, 10);
    $obPDF->addQuebraPagina('pagina', 1);

    $obPDF->setAlinhamento ('C');
    $obPDF->addCampo       ('data', 8);
    $obPDF->setAlinhamento ('L');
    $obPDF->addCampo       ('descricao', 8);
    $obPDF->setAlinhamento ('R');
    $obPDF->addCampo       ('valor', 8);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCampo       ('saldo', 8);
}

$obPDF->addRecordSet( $arDados[1] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho( "", 6, 10 );
$obPDF->addCabecalho( "",35, 10 );
$obPDF->addCabecalho( "",10, 10 );
$obPDF->addCabecalho( "",30, 10 );
$obPDF->addCabecalho( "",10, 10 );

$obPDF->addCampo(""         , 7 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao", 7 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"    , 7 );
$obPDF->addCampo("descricao_liquido", 7 );
$obPDF->addCampo("valor_liquido", 7 );

$i = 0;

$arAssinaturas = Sessao::read('assinaturas');

if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();

?>
