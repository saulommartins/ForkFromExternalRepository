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
    * Página de Visão de Manter Região
    * Data de Criação   : 22/09/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Marcio Medeiros

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso: uc-02.09.03
*/
include_once 'VPPAUtils.class.php';

class VPPAManterRegiao
{
    private $obNegocio;

    public function __construct(RPPAManterRegiao $obNegocio)
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
     * Verifica se já existe uma Região cadastrada com o mesmo nome.
     *
     * @param  array  $arParams Global REQUEST
     * @return string Javascript
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     */
    public function checarCadastroRegiao(array $arParams)
    {
        $obVPPAUtils    = new VPPAUtils;
        $stNomeOriginal = trim($arParams['stNome']);
        $stNomeTratado  = $obVPPAUtils->retirarAcento($stNomeOriginal);
        $numNomesCadastrados = $this->obNegocio->checarCadastroRegiao($stNomeOriginal, $stNomeTratado);
        if ($numNomesCadastrados > 0) {
            $stAviso  = 'Região "'.$arParams['stNome'].'" já cadastrada!';
            $stJs  = " alertaAviso('$stAviso', 'form', 'erro', '". Sessao::getId() ."');    \n";
            $stJs .= " document.getElementById('stNomeRegiao').value = '';                  \n";

            return $stJs;
        }
    }

    /**
     * Monta lista de Regiões já cadastradas.
     */
    public function montarListaRegioes($rsRegioes, $boAcao = false, $stAcao = '')
    {
        $obLista = new Lista();

        if (!$boAcao) {
            $obLista->setMostraPaginacao(false);
        }

        $obLista->setTitulo('Lista de Regiões');

        $obLista->setRecordSet($rsRegioes);

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(5);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Código da Região');
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Nome da Região');
        $obLista->ultimoCabecalho->setWidth(80);
        $obLista->commitCabecalho();

        if ($boAcao) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth(5);
            $obLista->commitCabecalho();
        }

        # Dados
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->ultimoDado->setCampo('cod_regiao');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('nome');
        $obLista->commitDado();

        if ($boAcao) {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao($stAcao);
            $obLista->ultimaAcao->addCampo('&inCodRegiao', 'cod_regiao');
            $obLista->ultimaAcao->addCampo('&stNome', 'nome');
            $obLista->ultimaAcao->addCampo('&stDescricao', 'descricao');
            $obLista->ultimaAcao->addCampo("&stDescQuestao"  ,"cod_regiao");

            if ($stAcao == 'excluir') {
                $stCaminho = CAM_GF_PPA_INSTANCIAS . 'regioes/PRManterRegioes.php?' . Sessao::getID() . '&stAcao=' . $stAcao;
                $obLista->ultimaAcao->setLink($stCaminho);
            } else {
                $stCaminho = 'FMManterRegioes.php?' . Sessao::getID() . '&stAcao=' . $stAcao;
                $obLista->ultimaAcao->setLink($stCaminho);
            }

            $obLista->commitAcao();
        }

        return $obLista;
    }

    /**
     *
     */
    public function listarRegioes(Request $request)
    {
        $rsRegioes = $this->obNegocio->getListaRegioes();
        $obLista = $this->montarListaRegioes($rsRegioes, false);
        $obLista->montaHTML();

        return "$('spnListaRegioes').innerHTML = '".$this->quoteJavascript($obLista->getHTML())."'";
    }

    public function getListaRegioes()
    {
        return $this->obNegocio->getListaRegioes();
    }

    public function listar(Request $request)
    {
        $stNome = stripslashes($request->get('stNome'));
        $stDescricao = stripslashes($request->get('stDescricao'));
        $rsRegioes = $this->obNegocio->getListaRegioes('', $stNome, $stDescricao);
        $obLista = $this->montarListaRegioes($rsRegioes, true, $request->get('stAcao'));
        $obLista->show();
    }

    public function incluir(Request $request)
    {
        $obErro = $this->obNegocio->incluir($request);
        if ($obErro->ocorreu()) {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_incluir', 'erro');
        } else {
            $pgDestino = 'FMManterRegioes.php?stAcao=' . $request->get('stAcao');
            SistemaLegado::alertaAviso($pgDestino, $request->get('inCodRegiao'), 'incluir', 'aviso', Sessao::getId(), '../');
        }
    }

    public function alterar(Request $request)
    {
        $obErro = $this->obNegocio->alterar($request);
        if ($obErro->ocorreu()) {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_alterar', 'erro');
        } else {
            $pgDestino = 'LSManterRegioes.php?stAcao=' . $request->get('stAcao');
            SistemaLegado::alertaAviso($pgDestino, $request->get('inCodRegiao'), 'alterar', 'aviso', Sessao::getId(), '../');
        }
    }

    public function excluir(Request $request)
    {
        $arRetorno = $this->obNegocio->excluir($request);

        if ($arRetorno['boOcorreu']) {
            SistemaLegado::alertaAviso('LSManterRegioes.php?stAcao=excluir', $arRetorno['stMensagem'], $arRetorno['stAcao'], 'aviso', Sessao::getId());
        } else {
            SistemaLegado::alertaAviso('LSManterRegioes.php?stAcao=excluir', $request->get('inCodRegiao'), 'excluir', 'aviso', Sessao::getId());
        }
    }

    /**
     * Executa ação recebida na página de processamento (PR).
     */
    public function executarAcao(Request $request)
    {
        Sessao::setTrataExcecao( true );

        $stMetodo = $request->get('stAcao');

        if (is_string($stMetodo)) {
            $this->$stMetodo($request);
        }

        Sessao::encerraExcecao();
    }
}

?>
