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
 * Classe de negócio do UC-02.10.05
 * Data de Criação: 17/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.05 - Manter Ajuste de Anexo
 */
include_once CAM_GF_LDO_NEGOCIO    . 'RLDOPadrao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoEntidade.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDONotaExplicativa.class.php';

class RLDOManterNotaExplicativa extends RLDOPadrao implements IRLDOPadrao
{
    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    protected function inicializar() {}

    private function salvarNotaExplicativa($stAcao, $inCodNotaExplicativa, $stNotaExplicativa, $inCodAnexo, $inAnoLDO, $inNumCGM, $boTransacao = '')
    {
        $obMapeamento = new TLDONotaExplicativa();

        if (!$inCodNotaExplicativa) {
            $obErro = $obMapeamento->proximoCod($inCodNotaExplicativa, $boTransacao);
            if ($obErro->ocorreu()) {
                throw new RLDOExcecao('Erro ao tentar obter próximo código de Nota Explicativa.', $this->recuperarAnotacoes());
            }
        }

        # Salva Nota Explicativa na tabela
        $obMapeamento->setDado('cod_nota_explicativa', $inCodNotaExplicativa);
        $obMapeamento->setDado('descricao', $stNotaExplicativa);
        $obMapeamento->setDado('numcgm', $inNumCGM);
        $obMapeamento->setDado('cod_anexo', $inCodAnexo);
        $obMapeamento->setDado('ano', $inAnoLDO);
        $obErro = $obMapeamento->$stAcao($boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar inclur Nota Explicativa.', $this->recuperarAnotacoes());
        }

        return $inCodNotaExplicativa;
    }

    private function excluirNotaExplicativa($inCodNotaExplicativa, $boTransacao = '')
    {
        $obMapeamento = new TLDONotaExplicativa();
        $obMapeamento->setDado('cod_nota_explicativa', $inCodNotaExplicativa);
        $obErro = $obMapeamento->exclusao($boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar excluir Nota Explicativa.', $this->recuperarAnotacoes());
        }

        return $inCodNotaExplicativa;
    }

    /**
     * Recupera CGM a partir do código da Entidade
     *
     * @param $inExercicio ano de exercício
     * @param $inCodEntidade o código da Entidade
     * @param $boTransacao se houver transação ativa
     * @return integer o número do CGM
     */
    private function recuperarCGMEntidade($inExercicio, $inCodEntidade, $boTransacao = '')
    {
        $stFiltro = " WHERE cod_entidade = $inCodEntidade AND exercicio = '$inExercicio'";

        $obMapeamento = new TOrcamentoEntidade();
        $obErro = $obMapeamento->recuperaTodos($rsEntidade, $stFiltro, '', $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar obter GCM da Entidade.', $this->recuperarAnotacoes());
        }

        if ($rsEntidade->eof()) {
            throw new RLDOExcecao('CGM da Entidade não encontrado.', $this->recuperarAnotacoes());
        }

        return $rsEntidade->getCampo('numcgm');
    }

    /**
     * Recupera código da Entidade a partir do CGM
     *
     * @param $inExercicio ano de exercício
     * @param $inNumCGM o número do CGM
     * @param $boTransacao se houver transação ativa
     * @return integer o código da Entidade
     */
    private function recuperarEntidadeCGM($inExercicio, $inNumCGM, $boTransacao = '')
    {
        $stFiltro = " WHERE numcgm = $inNumCGM AND exercicio = '$inExercicio'";

        $obMapeamento = new TOrcamentoEntidade();
        $obErro = $obMapeamento->recuperaTodos($rsEntidade, $stFiltro, '', $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar obter o código da Entidade a partir do GCM.', $this->recuperarAnotacoes());
        }

        if ($rsEntidade->eof()) {
            throw new RLDOExcecao('Código da Entidade não encontrado.', $this->recuperarAnotacoes());
        }

        return $rsEntidade->getCampo('cod_entidade');
    }

    /**
     * Inclui uma nova Nota Explicativa
     * @param $arArgs array contendo todos os argumentos
     * @return integer o código da notação explicativa incluída
     */
    public function incluir(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar iniciar transação.', $this->recuperarAnotacoes());
        }

        # Obtem o CGM pelo número da entidade e ano de exercício.
        $inNumCGM = $this->recuperarCGMEntidade(Sessao::getExercicio(), $arArgs['inCodEntidade'], $boTransacao);

        $inCodNotaExplicativa = $this->salvarNotaExplicativa('inclusao', null, $arArgs['stNotaExplicativa'], $arArgs['inCodAnexo'], $arArgs['inAnoLDO'], $inNumCGM, $boTransacao);

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $arArgs);

        # Retorno da operação: o código da nova notação explicativa.

        return $inCodNotaExplicativa;
    }

    /**
     * Exclui uma Nota Explicativa
     * @param $arArgs array contendo todos os argumentos
     * @return integer o código da notação explicativa incluída
     */
    public function excluir(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar iniciar transação.', $this->recuperarAnotacoes());
        }

        $inCodNotaExplicativa = $this->excluirNotaExplicativa($arArgs['inCodNotaExplicativa'], $boTransacao);

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, new TLDONotaExplicativa());

        # Retorno da operação: o código da notação explicativa.

        return $inCodNotaExplicativa;
    }

    /**
     * Recupera todos os dados relativos a uma Nota Explicativa.
     *
     * @param $inCodNotaExplicativa código da Nota Explicativa
     * @param $boTransacao se houve transação
     * @return array atributos da Nota Explicativa
     */
    private function recuperarNotaExplicativa($inCodNotaExplicativa, $boTransacao = '')
    {
        $stFiltro = ' WHERE cod_nota_explicativa = ' . $inCodNotaExplicativa;
        $stOrdem  = '';

        $obMapeamento = new TLDONotaExplicativa();
        $obErro = $obMapeamento->recuperaRelacionamento($rsNotaExplicativa, $stFiltro, $stOrdem, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar obter Nota Explicativa.', $this->recuperarAnotacoes());
        }

        return array_pop($rsNotaExplicativa->getElementos());
    }

    public function recuperarDados(&$arArgs, $boTransacao = '')
    {
        $arDados = $this->recuperarNotaExplicativa($arArgs['inCodNotaExplicativa'], $boTransacao);

        if (!$arDados) {
            return false;
        }

        # Preenche os dados obtidos para o array.
        $arArgs['inNumCGM']          = $arDados['numcgm'];
        $arArgs['stNomCGM']          = $arDados['nom_cgm'];
        $arArgs['inAnoLDO']          = $arDados['ano'];
        $arArgs['stNotaExplicativa'] = $arDados['descricao'];
        $arArgs['inCodAnexo']        = $arDados['cod_anexo'];
        $arArgs['stNomAcao']         = $arDados['nom_acao'];
        $arArgs['inCodEntidade']     = $arDados['cod_entidade'];

        return true;
    }

    public function alterar(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar iniciar transação.', $this->recuperarAnotacoes());
        }

        # Guarda aquilo que será modificado.
        $stNotaExplicativa = $arArgs['stNotaExplicativa'];

        # Obtem os dados restantes para salvar a Nota Explicativa.
        $this->recuperarDados($arArgs, $boTransacao);

        $inCodNotaExplicativa = $this->salvarNotaExplicativa('alteracao', $arArgs['inCodNotaExplicativa'], $stNotaExplicativa, $arArgs['inCodAnexo'], $arArgs['inAnoLDO'], $arArgs['inNumCGM'], $boTransacao);

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, new TLDONotaExplicativa());

        # Retorno da operação: o código da nova notação explicativa.

        return $inCodNotaExplicativa;
    }

    /**
     * Recupera lista de Notas Explicativas
     *
     * @param  array     $arParametros
     * @return RecordSet
     */
    public function recuperarListaNotaExplicativa($inCodAnexo = '', $boTransacao = '')
    {
        if ($inCodAnexo) {
            $stFiltro = ' WHERE cod_anexo = ' . $inCodAnexo;
        }

        $obMapeamento = new TLDONotaExplicativa();
        $obErro = $obMapeamento->recuperaTodos($rsListaNotaExplicativa, $stFiltro, $stOrdem, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao ler lista de Notas Explicativas', $this->recuperarAnotacoes());
        }

        return $rsListaNotaExplicativa;
    }

}
