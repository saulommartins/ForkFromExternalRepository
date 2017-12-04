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
 * Classe Visão de Manter Produto
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 */
include_once 'VPPAUtils.class.php';

class VPPAManterProduto
{
    private $obNegocio;

    public function __construct(RPPAManterProduto $obNegocio)
    {
        $this->obNegocio = $obNegocio;
    }

    private function quoteJavascript($stJS)
    {
        $stJS = str_replace( "  ", "", $stJS );
        $stJS = str_replace( "\n", "", $stJS );
        $stJS = str_replace( "'", "\\'", $stJS );

        return $stJS;
    }

    /**
     * Verifica se já existe um Produto cadastrado com a mesma descricao.
     *
     * @param  array  $arParams Global REQUEST
     * @return string Javascript
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     */
    public function checarCadastroProduto(array $arParams)
    {
        $obVPPAUtils    = new VPPAUtils;
        $stDescOriginal = trim($arParams['stDescricao']);
        $stDescTratado  = $obVPPAUtils->retirarAcento($stDescOriginal);
        $numRegistros   = $this->obNegocio->checarCadastroProduto($stDescOriginal, $stDescTratado);
        if ($numRegistros > 0) {
            $stAviso = 'Produto "'.$arParams['stDescricao'].'" já cadastrado!';
            $stJs    = " alertaAviso('$stAviso', 'form', 'erro', '". Sessao::getId() ."');    \n";
            $stJs   .= " document.getElementById('stDescricao').value = '';                  \n";

            return $stJs;
        }
    }

    /**
     * Monta lista de Produtos cadastrados.
     */
    public function montarListaProdutos($rsProdutos, $boAcao = false, $stAcao = '')
    {
        $obLista = new Lista();
        $obLista->setMostraPaginacao(false);
        $obLista->setTitulo('Lista de Produtos');

        $obLista->setRecordSet($rsProdutos);

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(5);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Código da Produto');
        $obLista->ultimoCabecalho->setWidth(30);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Descrição do Produto');
        $obLista->ultimoCabecalho->setWidth(50);
        $obLista->commitCabecalho();

        if ($boAcao) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth(5);
            $obLista->commitCabecalho();
        }

        # Dados
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('cod_produto');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('descricao');
        $obLista->commitDado();

        if ($boAcao) {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao($stAcao);
            $obLista->ultimaAcao->addCampo('&inCodProduto', 'cod_produto');
            $obLista->ultimaAcao->addCampo('&stDescricao', 'descricao');
            $obLista->ultimaAcao->addCampo("&stDescQuestao"  ,"cod_produto");

            if ($stAcao == 'excluir') {
                $stCaminho = CAM_GF_PPA_INSTANCIAS . 'produtos/PRManterProdutos.php?' . Sessao::getID() . '&stAcao=' . $stAcao;
                $obLista->ultimaAcao->setLink($stCaminho);
            } else {
                $stCaminho = 'FMManterProdutos.php?' . Sessao::getID() . '&stAcao=' . $stAcao;
                $obLista->ultimaAcao->setLink($stCaminho);
            }

            $obLista->commitAcao();
        }

        return $obLista;
    }

    /**
     *
     */
    public function getListaProdutos()
    {
        return $this->obNegocio->getListaProdutos();
    }

    /**
     *
     */
    public function listarProdutos()
    {
        $rsProdutos = $this->obNegocio->getListaProdutos();
        $obLista = $this->montarListaProdutos($rsProdutos, false);
        $obLista->montaHTML();

        return "$('spnListaProdutos').innerHTML = '".$this->quoteJavascript($obLista->getHTML())."'";
    }

    public function listar()
    {
        $stDescricao = stripslashes($_REQUEST['stDescricao']);
        $rsProdutos = $this->obNegocio->getListaProdutos('', $stDescricao);
        $obLista = $this->montarListaProdutos($rsProdutos, true, $_REQUEST['stAcao']);
        $obLista->show();
    }

    public function incluir()
    {
        $resultado = $this->obNegocio->checarCadastroProduto($_REQUEST['stDescricao'],$_REQUEST['stDescricao']);
        if ($resultado > 0) {
            SistemaLegado::LiberaFrames(true,false);

            return SistemaLegado::exibeAviso('Produto '.$_REQUEST['stDescricao'].' já incluido', 'n_incluir', 'erro');
        }

        $obErro = $this->obNegocio->incluir($_REQUEST);
        if ($obErro->ocorreu()) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_incluir', 'erro');
        } else {
            $pgDestino = 'FMManterProdutos.php?stAcao=' . $_REQUEST['stAcao'];
            SistemaLegado::LiberaFrames(true,false);

            SistemaLegado::exibeAviso("Produto ".$_REQUEST['stDescricao']." incluido com sucesso", 'incluir', 'aviso');
            SistemaLegado::mudaFramePrincipal($pgDestino);
        }
    }

    public function alterar()
    {
        $obErro = $this->obNegocio->alterar($_REQUEST);
        if ($obErro->ocorreu()) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_alterar', 'erro');
        } else {
            $pgDestino = 'LSManterProdutos.php?stAcao=' . $_REQUEST['stAcao'];
            SistemaLegado::alertaAviso($pgDestino, $_REQUEST['inCodProduto'], 'alterar', 'aviso', Sessao::getId(), '../');
        }
    }

    public function excluir()
    {
        $arRetorno = $this->obNegocio->excluir($_REQUEST);

        if ($arRetorno['boOcorreu']) {
            SistemaLegado::alertaAviso('LSManterProdutos.php?stAcao=excluir', $arRetorno['stMensagem'], $arRetorno['stAcao'], 'aviso', Sessao::getId());
        } else {
            SistemaLegado::alertaAviso('LSManterProdutos.php?stAcao=excluir', $_REQUEST['inCodProduto'], 'excluir', 'aviso', Sessao::getId());
        }
    }

    /**
     * Executa ação recebida na página de processamento (PR).
     */
    public function executarAcao()
    {
        Sessao::setTrataExcecao( true );

        $stMetodo = $_REQUEST['stAcao'];

        if (is_string($stMetodo)) {
            $this->$stMetodo($_REQUEST);
        }

        Sessao::encerraExcecao();
    }
}

?>
