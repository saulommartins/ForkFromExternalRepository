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
    * PÃ¡gina de Processamento dos ParÃ¢metros do Arquivo de Relacionamento das Despesas
    * Data de CriaÃ§Ã£o   : 24/10/2006

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Thiago La Delfa Cabelleira

    * @ignore

     $Id: PRManterHabilitacaoParticipante.php 61943 2015-03-17 16:32:31Z arthur $

    * Casos de uso: uc-03.05.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoParticipanteDocumentos.class.php");
include_once(TLIC."TLicitacaoParticipante.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterHabilitacaoParticipante";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao( true );

$obTLicitacaoParticipanteDocumentos = new TLicitacaoParticipanteDocumentos();
Sessao::getTransacao()->setMapeamento( $obTLicitacaoParticipanteDocumentos );

$stAcao = $request->get('stAcao');

switch ($_REQUEST['stAcao']) {
    case 'alterar':

        $obTLicitacaoParticipante = new TLicitacaoParticipante();
        $obTLicitacaoParticipante->setDado('cod_licitacao'	,$_REQUEST['cod_licitacao'	]);
        $obTLicitacaoParticipante->setDado('cod_modalidade' ,$_REQUEST['cod_modalidade'	]);
        $obTLicitacaoParticipante->setDado('cod_entidade' 	,$_REQUEST['cod_entidade'	]);
        $obTLicitacaoParticipante->setDado('exercicio' 		,$_REQUEST['exercicio'		]);
        $obTLicitacaoParticipante->recuperaPorChave( $rsLicitacaoParticipante );

        $inCount = 0;
        $arFornecedores = array();
        while ( !$rsLicitacaoParticipante->eof() ) {
            $inCodCgmFornecedor = $rsLicitacaoParticipante->getCampo('cgm_fornecedor');

            include_once CAM_GP_COM_MAPEAMENTO.'TComprasFornecedor.class.php';
            $obTComprasFornecedor = new TComprasFornecedor();
            $obTComprasFornecedor->setDado("cgm_fornecedor", $inCodCgmFornecedor);
            $obTComprasFornecedor->recuperaListaFornecedor( $rsFornecedor );

            if ($rsFornecedor->getCampo('status') == 'Inativo') {
                $arFornecedores[$inCount]['cgm_fornecedor'] = $inCodCgmFornecedor;
                $arFornecedores[$inCount]['nom_cgm'] = $rsFornecedor->getCampo('nom_cgm');
                $inCount++;
            }

            $rsLicitacaoParticipante->proximo();
        }
        $rsLicitacaoParticipante->setPrimeiroElemento();

        if (count($arFornecedores) > 0) {
            if (count($arFornecedores) == 1) {
                $stMensagemErro = 'O Participante ('.$arFornecedores[0]['cgm_fornecedor'].' - '.$arFornecedores[0]['nom_cgm'].') está inativo! Efetue a Manutenção de Participantes para retirar este Participante.';
            } elseif (count($arFornecedores) > 1) {
                foreach ($arFornecedores as $arFornecedoresAux) {
                    $stCodNomFornecedores .= $arFornecedoresAux['cgm_fornecedor'].' - '.$arFornecedoresAux['nom_cgm'].', ';
                }
                $stCodNomFornecedores = substr($stCodNomFornecedores, 0, strlen($stCodNomFornecedores)-2);
                $stMensagemErro = 'Os Participantes ('.$stCodNomFornecedores.') estão inativos! Efetue a Manutenção de Participantes para retirar estes Participantes.';
            }
        }

        $obTLicitacaoParticipanteDocumentos = new TLicitacaoParticipanteDocumentos();
        $obTLicitacaoParticipanteDocumentos->setDado('cod_licitacao',$_REQUEST['cod_licitacao']);
        $obTLicitacaoParticipanteDocumentos->setDado('cod_modalidade',$_REQUEST['cod_modalidade']);
        $obTLicitacaoParticipanteDocumentos->setDado('cod_entidade',$_REQUEST['cod_entidade']);
        $obTLicitacaoParticipanteDocumentos->setDado('exercicio',$_REQUEST['exercicio']);
        $obTLicitacaoParticipanteDocumentos->exclusao();

        $inCountNulos = 0;
        while ( !$rsLicitacaoParticipante->eof() ) {

            $arDocumentoParticipante = Sessao::read('arDocumentoParticipante_'.$rsLicitacaoParticipante->getCampo('cgm_fornecedor'));

            for ($i=0;$i<count($arDocumentoParticipante);$i++) {
                if ($arDocumentoParticipante[$i]['num_documento'] != '') {
                    $obTLicitacaoParticipanteDocumentos->setDado('cod_licitacao',$_REQUEST['cod_licitacao'] );
                    $obTLicitacaoParticipanteDocumentos->setDado('cod_documento',$arDocumentoParticipante[$i]['cod_documento'] );
                    $obTLicitacaoParticipanteDocumentos->setDado('dt_validade',$arDocumentoParticipante[$i]['dt_validade'] );
                    $obTLicitacaoParticipanteDocumentos->setDado('cgm_fornecedor',$arDocumentoParticipante[$i]['numcgm'] );
                    $obTLicitacaoParticipanteDocumentos->setDado('cod_modalidade',$_REQUEST['cod_modalidade']);
                    $obTLicitacaoParticipanteDocumentos->setDado('cod_entidade',$_REQUEST['cod_entidade']);
                    $obTLicitacaoParticipanteDocumentos->setDado('exercicio',$_REQUEST['exercicio']);
                    $obTLicitacaoParticipanteDocumentos->setDado('num_documento', $arDocumentoParticipante[$i]['num_documento']);
                    $obTLicitacaoParticipanteDocumentos->setDado('dt_emissao',$arDocumentoParticipante[$i]['dt_emissao'] );
                    $obTLicitacaoParticipanteDocumentos->inclusao();
                } else {
                    $inCountNulos++;
                }
            }
            $rsLicitacaoParticipante->proximo();
        }

        if ($inCountNulos > 0) {
            $stAlerta = ' (Existem participantes com documento(s) em branco) ';
        }

        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Habilitação de participante gravado com sucesso!".$stAlerta.$stMensagemErro,"alterar","aviso", Sessao::getId(), "../");

    break;
}//fim do switch
Sessao::encerraExcecao();
?>
