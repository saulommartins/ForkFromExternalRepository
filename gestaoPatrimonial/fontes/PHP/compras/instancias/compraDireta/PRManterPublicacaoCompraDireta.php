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
    * Página de Processamento de publicacao de compra direta
    * Data de Criação   : 03/08/2015

    * @author Analista: Gelson Goncalves
    * @author Desenvolvedor: Lisiane Morais

    * @ignore
    * Casos de uso : uc-03.05.17
    
    $Id:$

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(  TCOM."TComprasPublicacaoCompraDireta.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPublicacaoCompraDireta";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LSManterCompraDireta.php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";
//controle de acao deste arquivo
$stAcao = $request->get('stAcao');

if ($stAcao == '') {
    $stAcao = 'publicar';
}

//inicializar transação
Sessao::setTrataExcecao( true );
//instancia objeto de persistencia/mapeamento
$obTComprasPublicacaoCompraDireta = new TComprasPublicacaoCompraDireta();
Sessao::getTransacao()->setMapeamento( $obTComprasPublicacaoCompraDireta );

switch ($stAcao) {

  case 'publicar':
    $arVeiculos = Sessao::read('arVeiculos');
    $inTotalVeiculos = count($arVeiculos);
    if ($inTotalVeiculos <= 0) {
        sistemaLegado::exibeAviso( 'É preciso incluir ao menos um veículo de publicação.'   ,"n_incluir","erro");
    } else {
        //primeiramente exclui todos os registros relacionados ao edital
        $obTComprasPublicacaoCompraDireta->setDado("exercicio_entidade", $request->get('entidade_exercicio'));
        $obTComprasPublicacaoCompraDireta->setDado("cod_compra_direta", $request->get('inCodCompraDireta'));
        $obTComprasPublicacaoCompraDireta->setDado("cod_entidade", $request->get('inCodEntidade'));
        $obTComprasPublicacaoCompraDireta->setDado("cod_modalidade", $request->get('inCodModalidade'));
        $obTComprasPublicacaoCompraDireta->recuperaTodos($rsVeiculosPublicidade,'','',$boTransacao);
        
        while ( !$rsVeiculosPublicidade->eof() ) {
            $obTComprasPublicacaoCompraDireta->setDado("exercicio_entidade", $request->get('entidade_exercicio'));
            $obTComprasPublicacaoCompraDireta->setDado("cod_compra_direta", $request->get('inCodCompraDireta'));
            $obTComprasPublicacaoCompraDireta->setDado("cod_entidade", $request->get('inCodEntidade'));
            $obTComprasPublicacaoCompraDireta->setDado("cod_modalidade", $request->get('inCodModalidade'));
            $obTComprasPublicacaoCompraDireta->exclusao($boTransacao);
            $rsVeiculosPublicidade->proximo();
        }

        for ($i=0; $i < $inTotalVeiculos; $i++) {
          $item = $arVeiculos[$i];
          $obTComprasPublicacaoCompraDireta->setDado("cgm_veiculo",(int) ($item['veiculoPublicacao']));
          $obTComprasPublicacaoCompraDireta->setDado("data_publicacao",addSlashes(trim($item['dataPublicacao'])));
          $obTComprasPublicacaoCompraDireta->setDado("observacao",html_entity_decode(trim($item['observacao'])));
          $obTComprasPublicacaoCompraDireta->setDado("num_publicacao", $item['inNumPublicacao']);
          $obTComprasPublicacaoCompraDireta->inclusao($boTransacao);
        }
        
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Publicação de Compras Direta concluído com sucesso! (Compra Direta ".$request->get('inCodCompraDireta').")","aviso","aviso", Sessao::getId(), "../");
        break;
    }
}
Sessao::encerraExcecao();
?>
