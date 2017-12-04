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
 * Classe Visão Validar Acao
 *
 * @author Henrique Boaventura <henrique.boaventura@cnm.org.br>
 *
 */

require_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';
require_once CAM_GF_LDO_MAPEAMENTO.'TLDOHomologacao.class.php';

class VLDOValidarAcao
{
    public $obModel;

    /**
     * Metodo construtor, seta o atributo obModel com o que vier na assinatura da funcao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $obModel Classe de Negocio
     *
     * @return void
     */
    public function __construct(RLDOValidarAcao $obModel)
    {
        $this->obModel= $obModel;
    }

    public function validateData(array $arParam)
    {
        $obErro = new Erro();

        if (!isset($arParam['chkValidar'])) {
            $obErro->setDescricao('Deve ser marcado algum item para a validação da ação.');
        } else {

            $inCount = 1;
            foreach ($arParam['chkValidar'] as $inCodRecurso => $stValue) {
                if ($stValue == 'on') {
                    if (empty($arParam['flMeta'][$inCodRecurso])) {
                        $obErro->setDescricao('Deve ser preenchido um valor para a Meta da linha '.$inCount.'.');
                        break;
                    } elseif ($this->formatValue($arParam['flMeta'][$inCodRecurso]) > $this->formatValue($arParam['flMetaDisponivel'][$inCodRecurso])) {
                        $stDescricao  = 'O valor da Meta da linha '.$inCount.' ('.$arParam['flMeta'][$inCodRecurso].') ';
                        $stDescricao .= 'é maior que o valor da Meta Disponível ('.$arParam['flMetaDisponivel'][$inCodRecurso].')';
                        $obErro->setDescricao($stDescricao);
                        break;
                    } elseif (empty($arParam['flValor'][$inCodRecurso])) {
                        $obErro->setDescricao('Deve ser preenchido um valor para o Valor Unitário da linha '.$inCount.'.');
                        break;
                    }
                    $inCount++;
                }
            }
        }

        return $obErro;
    }

    public function formatValue($stValue)
    {
        return str_replace(',', '.', str_replace('.', '', $stValue));
    }

    /**
     * Metodo inclui a validacao da acao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $arParam     array
     * @param object $boTransacao Transacao
     *
     * @return void
     */
    public function incluir(array $arParam, $boTransacao = '')
    {
        $obErro = $this->validateData($arParam);

        if (!$obErro->ocorreu()) {

            foreach ($arParam['chkValidar'] as $inCodRecurso => $stValue) {
                if ($stValue == 'on') {
                    $this->obModel->obRPPAManterAcao->inCodAcao            = $arParam['inCodAcao'];
                    $this->obModel->obRPPAManterAcao->inAno                = $arParam['inAno'];
                    $this->obModel->obRPPAManterAcao->stTimestampAcaoDados = $arParam['stTimestamp'];
                    $this->obModel->obRPPAManterAcao->stExercicioRecurso   = $arParam['stExercicio'];
                    $this->obModel->obRPPAManterAcao->inCodRecurso         = $inCodRecurso;
                    $this->obModel->flQuantidade                           = $this->formatValue($arParam['flMeta'][$inCodRecurso]);
                    $this->obModel->flValor                                = $this->formatValue($arParam['flValor'][$inCodRecurso]);

                    $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA   = $arParam['inCodPPA'];
                    $this->obModel->obRLDOLDO->inAno                       = $arParam['inAno'];
                    $obErro = $this->obModel->incluir();

                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('LSValidarAcao.php'.'?'.Sessao::getId().'&stAcao='.$arParam['stAcao'], 'Ação - '.$arParam['inNumAcao'], $arParam['stAcao'],'aviso', Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso('Ocorreu um erro no processo de valição', 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo altera a validacao da acao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $arParam     array
     * @param object $boTransacao Transacao
     *
     * @return void
     */
    public function alterar(array $arParam, $boTransacao = '')
    {
        $obErro = $this->validateData($arParam);

         if (!$obErro->ocorreu()) {

            foreach ($arParam['chkValidar'] as $inCodRecurso => $stValue) {
                if ($stValue == 'on') {

                    $this->obModel->obRPPAManterAcao->inCodAcao            = $arParam['inCodAcao'];
                    $this->obModel->obRPPAManterAcao->inAno                = $arParam['inAno'];
                    $this->obModel->obRPPAManterAcao->stTimestampAcaoDados = $arParam['stTimestamp'];
                    $this->obModel->obRPPAManterAcao->stExercicioRecurso   = $arParam['stExercicio'];
                    $this->obModel->obRPPAManterAcao->inCodRecurso         = $inCodRecurso;
                    $this->obModel->flQuantidade                           = $this->formatValue($arParam['flMeta'][$inCodRecurso]);
                    $this->obModel->flValor                                = $this->formatValue($arParam['flValor'][$inCodRecurso]);

                    $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA   = $arParam['inCodPPA'];
                    $this->obModel->obRLDOLDO->inAno                       = $arParam['inAno'];

                    $obErro = $this->obModel->alterar();

                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('LSValidarAcao.php?'.Sessao::getId().'&stAcao='.$arParam['stAcao'], 'Ação - '.$arParam['inNumAcao'], 'alterar','aviso', Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso('Ocorreu um erro no processo de alterar a validação', 'n_alterar', 'erro');
        }
    }

    /**
     * Metodo exclui a validacao da acao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $arParam     array
     * @param object $boTransacao Transacao
     *
     * @return void
     */
    public function excluir(array $arParam, $boTransacao = '')
    {
        $obErro = new Erro();

        $this->obModel->obRPPAManterAcao->inCodAcao            = $arParam['inCodAcao'];
        $this->obModel->obRPPAManterAcao->inAno                = $arParam['inAno'];
        $this->obModel->obRPPAManterAcao->stTimestampAcaoDados = $arParam['stTimestamp'];
        $this->obModel->obRPPAManterAcao->stExercicioRecurso   = $arParam['stExercicioRecurso'];
        $this->obModel->obRPPAManterAcao->inCodRecurso         = $arParam['inCodRecurso'];

        $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA   = $arParam['inCodPPA'];
        $this->obModel->obRLDOLDO->inAno                       = $arParam['inAno'];

        if (!$obErro->ocorreu()) {
            $obErro = $this->obModel->excluir();
        }

        if (!$obErro->ocorreu()) {
            $rsAcao = new RecordSet;
            $this->getAcao($rsAcao, $arParam);
            if ($rsAcao->getNumLinhas() > 0) {
                unset($arParam['inCodRecurso']);
                unset($arParam['stExercicioRecurso']);
                $stLink  = 'FMValidarAcao.php'.'?'.Sessao::getId().'&'.http_build_query($arParam);
            } else {
                $stLink = 'LSValidarAcao.php'.'?'.Sessao::getId().'&stAcao='.$arParam['stAcao'];
            }
            $stMensagem = 'Ação - '.$arParam['inNumAcao'].'/ Recurso - '.str_pad($arParam['inCodRecurso'], 4, 0, STR_PAD_LEFT);
            SistemaLegado::alertaAviso($stLink, $stMensagem, 'excluir','aviso', Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso('Ocorreu um erro no processo de exclusão da valição', 'n_excluir', 'erro');
        }
    }

    /**
     * Metodo que monta a lista acoes nao validadas
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsAcao RecordSet
     *
     * @return object $obErro
     */
    public function listAcao(&$rsAcao,$arParam)
    {
        $this->obModel->obRPPAManterAcao->inCodAcao = $arParam['inNumAcaoInicio'];
        $this->obModel->obRPPAManterAcao->inCodAcaoFim = $arParam['inNumAcaoFim'];
        $this->obModel->obRPPAManterAcao->inCodRecurso = $arParam['inCodRecurso'];
        $this->obModel->obRPPAManterAcao->stTitulo = $arParam['stTitulo'];
        $this->obModel->obRPPAManterAcao->obRPPAManterPrograma->codPrograma = $arParam['inCodPrograma'];
        $this->obModel->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRPPAManterAcao->inAno = $arParam['slExercicioLDO'];

        $obErro = $this->obModel->listAcao($rsAcao,$arParam['stAcao']);

        return $obErro;
    }

    /**
     * Metodo que monta a lista acoes validadas para despesa
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object $rsAcao RecordSet
     *
     * @return object $obErro
     */
    public function listAcaoDespesa(&$rsAcao, $arParam, $stOrder = '')
    {
        $this->obModel->obRPPAManterAcao->inCodAcao = $arParam['inNumAcaoInicio'];
        $this->obModel->obRPPAManterAcao->inCodAcaoFim = $arParam['inNumAcaoFim'];
        $this->obModel->obRPPAManterAcao->inCodRecurso = $arParam['inCodRecurso'];
        $this->obModel->obRPPAManterAcao->stTitulo = $arParam['stTitulo'];
        $this->obModel->obRPPAManterAcao->obRPPAManterPrograma->codPrograma = $arParam['inCodPrograma'];
        $this->obModel->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRPPAManterAcao->inAno = $arParam['slExercicioLDO'];

        $obErro = $this->obModel->listAcaoDespesa($rsAcao, $stOrder);

        return $obErro;
    }

    /**
     * Metodo que retorna a acao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsAcao RecordSet
     *
     * @return object $obErro
     */
    public function getAcao(&$rsAcao, $arParam)
    {
        $this->obModel->obRPPAManterAcao->inCodAcao = $arParam['inCodAcao'];
        $this->obModel->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRPPAManterAcao->inAno = $arParam['inAno'];

        $obErro = $this->obModel->getAcao($rsAcao, $arParam['stAcao']);

        return $obErro;
    }

    /**
     * Metodo que preenche os dados do combo da LDO
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $arParam array
     *
     * @return void
     */
    public function preencheLDO(array $arParam)
    {
        $stJs  = "jq('#slExercicioLDO').removeOption(/./);";
        $stJs .= "var arOptions = {";
        if ($arParam['inCodPPA'] != '') {
            $obTPPA = new TPPA;
            $obTPPA->recuperaPPA($rsPPA,' WHERE cod_ppa = ' . $arParam['inCodPPA']);

            for ($i = $rsPPA->getCampo('ano_inicio'); $i <= $rsPPA->getCampo('ano_final'); $i++) {
                $stJs .= "'" . ($i - $rsPPA->getCampo('ano_inicio') + 1) . "' : '" . $i . "',";
            }
        }
        $stJs .= "};";
        $stJs .= "jq('#slExercicioLDO').addOption(arOptions,false);";

        return $stJs;
    }

    /**
     * Metodo que preenche os dados do combo da LDO com os exercícios homologados
     *
     * @author      Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @param object $arParam array
     *
     * @return void
     */
    public function preencheLDOHomologado(array $arParam)
    {
        $stJs  = "jq('#slExercicioLDO').removeOption(/./);";
        $stJs .= "var arOptions = {";
        if ($arParam['inCodPPA'] != '') {
            $obTLDOHomologacao = new TLDOHomologacao;
            $obTLDOHomologacao->setDado('cod_ppa', $arParam['inCodPPA']);
            $obTLDOHomologacao->recuperaLDOPorPPA($rsLDO);

            while (!$rsLDO->eof()) {
                $stJs .= "'".$rsLDO->getCampo('ano')."':'".$rsLDO->getCampo('exercicio')."',";
                $rsLDO->proximo();
            }
        }
        $stJs .= "};";
        $stJs .= "jq('#slExercicioLDO').addOption(arOptions,false);";

        return $stJs;
    }

    public function montaListagemRecursos(array $arParam)
    {
        $this->obModel->getListagemRecurso($rsListagem, $arParam);

        $obChkValidar = new CheckBox;
        $obChkValidar->setName ('chkValidar_[cod_recurso]');
        $obChkValidar->setId   ('chkValidar_[cod_recurso]');

        $obTxtMeta = new Numerico;
        $obTxtMeta->setName     ('flMeta_[cod_recurso]');
        $obTxtMeta->setId       ('flMeta_[cod_recurso]');
        $obTxtMeta->setMaxLength(14);
        $obTxtMeta->setSize     (14);
        $obTxtMeta->setNull     (false);
        $obTxtMeta->setValue    ('[quantidade_formatado]');
        $obTxtMeta->setStyle    ('text-align: right');
        $obTxtMeta->setLabel    (true);

        $obTxtValor = new Numerico;
        $obTxtValor->setName     ('flValor_[cod_recurso]');
        $obTxtValor->setId       ('flValor_[cod_recurso]');
        $obTxtValor->setMaxLength(14);
        $obTxtValor->setSize     (14);
        $obTxtValor->setNull     (false);
        $obTxtValor->setValue    ('[valor_formatado]');
        $obTxtValor->setStyle    ('text-align: right');
        $obTxtValor->setLabel    (true);

        $obTxtQuantidadeDisponivel = new Numerico;
        $obTxtQuantidadeDisponivel->setName     ('flMetaDisponivel_[cod_recurso]');
        $obTxtQuantidadeDisponivel->setId       ('flMetaDisponivel_[cod_recurso]');
        $obTxtQuantidadeDisponivel->setMaxLength(14);
        $obTxtQuantidadeDisponivel->setSize     (14);
        $obTxtQuantidadeDisponivel->setNull     (false);
        $obTxtQuantidadeDisponivel->setValue    ('[quantidade_disponivel]');
        $obTxtQuantidadeDisponivel->setStyle    ('text-align: right');
        $obTxtQuantidadeDisponivel->setLabel    (true);

        $rsRecurso = new RecordSet;
        $obTblRecursos = new Table;
        $obTblRecursos->setId('obTblRecursos');
        $obTblRecursos->setSummary('Dados da Ação '.$arParam['inNumAcao'].' - '.$arParam['stTitulo']);
        //$obTblRecursos->setConditional(true, "#efefef");
        $obTblRecursos->setRecordSet($rsListagem);
        $obTblRecursos->Head->addCabecalho ('Validar'        , 3);
        $obTblRecursos->Head->addCabecalho ('Recurso'        , 29);
        $obTblRecursos->Head->addCabecalho ('Meta'           , 8);
        $obTblRecursos->Head->addCabecalho ('Valor Total' , 8);
        $obTblRecursos->Head->addCabecalho ('Meta Disponível', 10);
        $obTblRecursos->Body->addComponente($obChkValidar    , 'C');
        $obTblRecursos->Body->addCampo     ('[cod_recurso] - [nom_recurso]', 'E');
        $obTblRecursos->Body->addComponente($obTxtMeta       , 'D');
        $obTblRecursos->Body->addComponente($obTxtValor      , 'D');
        $obTblRecursos->Body->addComponente($obTxtQuantidadeDisponivel, 'D');
        $obTblRecursos->montaHTML();

        return $obTblRecursos->getHtml();
    }

    public function montaListagemRecursosExcluir(array $arParam)
    {
        $this->obModel->getListagemRecurso($rsListagem, $arParam);

        $rsRecurso = new RecordSet;
        $obTblRecursos = new Table;
        $obTblRecursos->setId('obTblRecursos');
        $obTblRecursos->setSummary('Dados da Ação '.$arParam['inCodAcao'].' - '.$arParam['stTitulo']);
       // $obTblRecursos->setConditional(true, "#efefef");
        $obTblRecursos->setRecordSet($rsListagem);
        $obTblRecursos->Head->addCabecalho('Recurso'        , 35);
        $obTblRecursos->Head->addCabecalho('Meta'           , 15);
        $obTblRecursos->Head->addCabecalho('Valor Total' , 15);
        $obTblRecursos->Body->addCampo('[cod_recurso] - [nom_recurso]', 'E');
        $obTblRecursos->Body->addCampo('[quantidade_formatado]', 'D');
        $obTblRecursos->Body->addCampo('[valor_formatado]'     , 'D');
        $obTblRecursos->Body->addAcao('Excluir', "excluirRecurso(%s, '%s', %s, '%s')", array('cod_recurso', 'nom_recurso', 'cod_recurso', 'exercicio_recurso'));
        $obTblRecursos->montaHTML();

        return $obTblRecursos->getHtml();
    }
}
