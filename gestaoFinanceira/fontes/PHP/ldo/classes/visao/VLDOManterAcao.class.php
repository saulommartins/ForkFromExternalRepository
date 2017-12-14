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
 * Classe Visao do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package gestaoFinanceira
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once CAM_GF_LDO_UTIL    . 'LDOLista.class.php';
include_once CAM_GF_LDO_VISAO   . 'VLDOPadrao.class.php';
include_once CAM_GF_LDO_NEGOCIO . 'RLDOManterReceita.class.php';
include_once CAM_GF_LDO_NEGOCIO . 'RLDOManterAcao.class.php';
include_once(CAM_GF_PPA_NEGOCIO . 'RPPAManterAcao.class.php');

class VLDOManterAcao extends VLDOPadrao implements IVLDOPadrao
{
    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    public function inicializar()
    {
        parent::inicializarRegra(__CLASS__);
    }

    public function incluir(array $arParametros)
    {
        try {
            $inCodAcao = RLDOManterAcao::recuperarInstancia()->incluir($arParametros);
            SistemaLegado::alertaAviso('FMManterAcao.php?stAcao=incluir', $inCodAcao, 'incluir', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_incluir', 'erro', Sessao::getId(), '../');
            SistemaLegado::LiberaFrames(true,false);
        }
    }

    public function alterar(array $arParametros)
    {
        try {
            $inCodAcao = RLDOManterAcao::recuperarInstancia()->alterar($arParametros);
            SistemaLegado::alertaAviso('LSManterAcao.php?stAcao=alterar', $inCodAcao, 'alterar', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_alterar', 'erro', Sessao::getId(), '../');
            SistemaLegado::LiberaFrames(true,false);
        }
    }

    public function excluir(array $arParametros)
    {
        try {
            $inCodAcao = RLDOManterAcao::recuperarInstancia()->excluir($arParametros);
            SistemaLegado::alertaAviso('LSManterAcao.php?stAcao=excluir', $inCodAcao, 'excluir', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_excluir', 'erro', Sessao::getId(), '../');
            SistemaLegado::LiberaFrames(true,false);
        }
    }

    public function listar($inNumAcaoInicio = 0, $inNumAcaoFim = 0)
    {
        $rsAcao = RLDOManterAcao::recuperarInstancia()->listar($inNumAcaoInicio, $inNumAcaoFim);

        return $rsAcao;
    }

    public function recuperarAcaoLDO(array $arParametros)
    {
        $rsAcao = RLDOManterAcao::recuperarInstancia()->consultar($arParametros);

        return $rsAcao;
    }

    public function recuperarAcaoPPA(array $arParametros)
    {
        $rsAcao = RLDOManterAcao::recuperarInstancia()->recuperarAcaoPPA($arParametros);

        return $rsAcao;
    }

    public function inserirRecurso(array $arParametros)
    {
        unset($arParametros['stAno']);
        unset($arParametros['inCodAcao']);

        $arCabecalhos = array(
            array('cabecalho' => 'Codigo',  'width' => 8),
            array('cabecalho' => 'Recurso', 'width' => 40),
            array('cabecalho' => 'Conta',   'width' => 8),
            array('cabecalho' => 'Rubrica', 'width' => 18),
            array('cabecalho' => 'Valor',   'width' => 26)
        );

        $arComponentes = array(
            array('tipo'  => 'Label', 'name'  => 'arCodRecurso',   'campo' => 'inCodRecurso'),
            array('tipo'  => 'Label', 'name'  => 'arNomRecurso',   'campo' => 'stNomRecurso'),
            array('tipo'  => 'Label', 'name'  => 'arCodConta',     'campo' => 'inCodConta'),
            array('tipo'  => 'Label', 'name'  => 'arCodReceita',   'campo' => 'stCodReceita'),
            array('tipo'  => 'Label', 'name'  => 'arValorRecurso', 'campo' => 'flValorRecurso')
        );

        $rsRecurso = RLDOManterAcao::recuperarInstancia()->recuperarRecursoOrcamento($arParametros);

        if ($arParametros['inCodRecurso']) {
            $arValores[] = array(
                'inCodRecurso'   => $arParametros['inCodRecurso'],
                'stNomRecurso'   => $rsRecurso->getCampo('nom_recurso'),
                'inCodConta'     => $arParametros['inCodConta'],
                'stCodReceita'   => $arParametros['stCodReceita'],
                'flValorRecurso' => $arParametros['flValorRecurso']
            );
        } else {
            for ($x = 0; $x < count($arParametros); $x++) {
                $arValores[$x] = array(
                    'inCodRecurso'   => $arParametros[$x]['inCodRecurso'],
                    'stNomRecurso'   => $arParametros[$x]['stNomRecurso'],
                    'inCodConta'     => $arParametros[$x]['inCodConta'],
                    'stCodReceita'   => $arParametros[$x]['stCodReceita'],
                    'flValorRecurso' => $arParametros[$x]['flValorRecurso']
                );
            }
        }

        if (count($arParametros['arCodRecurso'])) {
            foreach ($arParametros['arCodRecurso'] as $inChave => $inValor) {
                $arLinha = array();
                foreach ($arComponentes as $arCampos) {
                    $arLinha[$arCampos['campo']] = $arParametros[$arCampos['name']][$inChave];
                }
                array_unshift($arValores, $arLinha);
            }
        }

        $stHTML = LDOLista::montarLista('Recurso', 'Lista de Recursos', $arCabecalhos, $arComponentes, $arValores);

        $stHTML = '$(\'spnListaRecurso\').innerHTML = \'' . $stHTML . '\';';
        $stHTML.= "document.getElementById('stCodReceita').value = '';";
        $stHTML.= "document.getElementById('inCodRecurso').value = '';";
        $stHTML.= "document.getElementById('flValorRecurso').value = '';";
        $stHTML.= "document.getElementById('stDescricaoReceita').innerHTML = '&nbsp;';";
        $stHTML.= "document.getElementById('stDescricaoRecurso').innerHTML = '&nbsp;';";

        return $stHTML;
    }

    public function montarRecurso(array $arParametros)
    {
        $boReadOnly = $boConsulta;

        $arCabecalhos = array(
            array('cabecalho' => 'Codigo',  'width' => 8),
            array('cabecalho' => 'Recurso', 'width' => 40),
            array('cabecalho' => 'Conta',   'width' => 8),
            array('cabecalho' => 'Rubrica', 'width' => 18),
            array('cabecalho' => 'Valor',   'width' => 26)
        );

        $arComponentes = array(
            array('tipo'  => 'Label',  'name'  => 'arCodRecurso',   'campo' => 'inCodRecurso'),
            array('tipo'  => 'Label',  'name'  => 'arNomRecurso',   'campo' => 'stNomRecurso'),
            array('tipo'  => 'Label',  'name'  => 'arCodConta',     'campo' => 'inCodConta'),
            array('tipo'  => 'Label',  'name'  => 'arCodReceita',   'campo' => 'stCodReceita'),
            array('tipo'  => 'Label',  'name'  => 'arValorRecurso', 'campo' => 'flValorRecurso')
        );

        $arValores = array();

        $rsRecurso = RLDOManterAcao::recuperarInstancia()->recuperarRecurso($arParametros);

        while (!$rsRecurso->eof()) {
            $arValores[] = array(
                'inCodRecurso'   => $rsRecurso->getCampo('cod_recurso'),
                'stNomRecurso'   => $rsRecurso->getCampo('nom_recurso'),
                'inCodConta'     => $rsRecurso->getCampo('cod_conta'),
                'stCodReceita'   => $rsRecurso->getCampo('mascara_classificacao'),
                'flValorRecurso' => $rsRecurso->getCampo('valor')
            );
            $rsRecurso->proximo();
        }

        $stHTML = LDOLista::montarLista('Recurso','Lista de Recursos', $arCabecalhos, $arComponentes, $arValores);

        return $stHTML;
    }

    public function validarDuplicidadeAcao(array $arParametros)
    {
        if (RLDOManterAcao::recuperarInstancia()->validarDuplicidadeAcao($arParametros)) {
            return false;
        }

        return true;
    }

    public function exibirPrograma(array $arParametros)
    {
        if (!$arParametros['inNumAcao']) {
            $stHTML = "document.getElementById('spnPrograma').innerHTML = '&nbsp;';";
        } elseif (!$this->validarDuplicidadeAcao($arParametros)) {
            $stHTML = "document.getElementById('spnPrograma').innerHTML = '&nbsp;';";
        } else {
            $stHTML = $this->montarPrograma($arParametros);
        }

        return $stHTML;
    }

    public function exibirTotalAcao(array $arParametros)
    {
        if (!$arParametros['inNumAcao']) {
            $stHTML = "document.getElementById('lbTotalAcao').innerHTML = '&nbsp';";
            $stHTML.= "document.getElementById('flTotalAcao').value     = '';";
        } else {
            $flTotal = $this->montarTotalAcoes($arParametros);
            $stHTML = "document.getElementById('lbTotalAcao').innerHTML = '{$flTotal}';";
            $stHTML.= "document.getElementById('flTotalAcao').value     = '{$flTotal}';";
        }

        return $stHTML;
    }

    public function montarTotalAcoes(array $arParametros)
    {
        $rsTotalAcao = RLDOManterAcao::recuperarInstancia()->recuperarTotalAcaoLDO($arParametros);

        return $rsTotalAcao->getCampo('total');
    }

    public function montarTotalReceita(array $arParametros)
    {
        $rsTotalReceita = RLDOManterReceita::recuperarInstancia()->recuperarTotalReceitaLDO($arParametros);

        return $rsTotalReceita->getCampo('total');
    }

    public function montarPrograma(array $arParametros)
    {
        $obForm = new Form();
        $obFormulario = new Formulario();

        $obLblNomPrograma = new Label;
        $obLblNomPrograma->setRotulo('Programa' );
        $obLblNomPrograma->setId('lbNomPrograma');
        $obLblNomPrograma->setValue($arParametros['stNomPrograma']);
        $obFormulario->addComponente($obLblNomPrograma);

        $obLblNumPrograma = new Label;
        $obLblNumPrograma->setRotulo('Identificação do Programa' );
        $obLblNumPrograma->setId('lbNumPrograma');
        $obLblNumPrograma->setValue($arParametros['inNumPrograma']);
        $obFormulario->addComponente($obLblNumPrograma);

        $obLblDiagnostico = new Label;
        $obLblDiagnostico->setRotulo('Diagnóstico' );
        $obLblDiagnostico->setId('lbDiagnostico');
        $obLblDiagnostico->setValue($arParametros['stDiagnostico']);
        $obFormulario->addComponente($obLblDiagnostico);

        $obLblObjetivo = new Label;
        $obLblObjetivo->setRotulo('Objetivo' );
        $obLblObjetivo->setId('lbObjetivo');
        $obLblObjetivo->setValue($arParametros['stObjetivo']);
        $obFormulario->addComponente($obLblObjetivo);

        $obLblDiretrizes = new Label;
        $obLblDiretrizes->setRotulo('Diretrizes' );
        $obLblDiretrizes->setId('lbDiretrizes');
        $obLblDiretrizes->setValue($arParametros['stDiretrizes']);
        $obFormulario->addComponente($obLblDiretrizes);

        $obLblPublico = new Label;
        $obLblPublico->setRotulo('Público Alvo' );
        $obLblPublico->setId('lbPublico');
        $obLblPublico->setValue($arParametros['stPublico']);
        $obFormulario->addComponente($obLblPublico);

        $obLblNatureza = new Label;
        $obLblNatureza->setRotulo('Natureza' );
        $obLblNatureza->setId('lbNatureza');
        $obLblNatureza->setValue($arParametros['stNatureza']);
        $obFormulario->addComponente($obLblNatureza);

        $obFormulario->addTitulo('Classificação de Funcional Programática');

        $obLblFuncao = new Label;
        $obLblFuncao->setRotulo('Função' );
        $obLblFuncao->setId('lbFuncao');
        $obLblFuncao->setValue($arParametros['inCodFuncao'] . " - " . $arParametros['stNomFuncao']);
        $obFormulario->addComponente($obLblFuncao);

        $obLblSubFuncao = new Label;
        $obLblSubFuncao->setRotulo('Subfunção' );
        $obLblSubFuncao->setId('lbSubfuncao');
        $obLblSubFuncao->setValue($arParametros['inCodSubfuncao'] . " - " . $arParametros['stNomSubfuncao']);
        $obFormulario->addComponente($obLblSubFuncao);

        $obLblPAO = new Label;
        $obLblPAO->setRotulo('PAO' );
        $obLblPAO->setId('lbPAO');
        $obLblPAO->setValue($arParametros['inCodTipoAcao'] . sprintf('%03d', $arParametros['inNumAcao']));
        $obFormulario->addComponente($obLblPAO);

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        return $stHTML;
    }
}
