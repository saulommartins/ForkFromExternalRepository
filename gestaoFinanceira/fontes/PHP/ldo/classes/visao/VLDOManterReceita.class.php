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
 * Classe de Visao do 02.10.04 - Manter Receita
 * Data de Criação: 17/02/209
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.04 - Manter Receita
 */

include_once CAM_GF_LDO_UTIL        . 'LDOLista.class.php';
include_once CAM_GF_LDO_VISAO       . 'VLDOPadrao.class.php';
include_once CAM_GF_LDO_NEGOCIO     . 'RLDOManterReceita.class.php';
include_once CAM_GF_PPA_NEGOCIO     . 'RPPAManterReceita.class.php';
include_once CAM_GF_LDO_COMPONENTES . 'IPopUpRecurso.class.php';

class VLDOManterReceita extends VLDOPadrao implements IVLDOPadrao
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
        if (!$this->existeListaRecurso($arParametros)) {
            sistemaLegado::exibeAviso('Nenhum recurso foi vinculado à Receita','n_incluir','aviso');
            exit();
        }
        try {
            RLDOManterReceita::recuperarInstancia()->incluir($arParametros);
            sistemaLegado::alertaAviso('FMManterReceita.php?stAcao=incluir',$arParametros['inNumReceita'],'incluir','aviso');
        } catch (RLDOExcecao $erro) {
            sistemaLegado::exibeAviso($erro->getMessage(), 'n_incluir', 'erro',Sessao::getId(), "../");
        }

    }

    private function existeListaRecurso(array $arParametros)
    {
        $boExiste = false;
        if ($arParametros['arCodRecurso']) {
            $boExiste = true;
        }

        return $boExiste;
    }

    public function excluir(array $arParametros)
    {
         try {
            RLDOManterReceita::recuperarInstancia()->excluir($arParametros);
            sistemaLegado::alertaAviso('LSManterReceita.php?stAcao=incluir',$arParametros['inNumReceita'],'incluir','aviso');
        } catch (RLDOExcecao $erro) {
            sistemaLegado::exibeAviso($erro->getMessage(), 'n_incluir', 'erro',Sessao::getId(), "../");
        }
    }

    public function alterar(array $arParametros)
    {
        if (!$this->existeListaRecurso($arParametros)) {
            sistemaLegado::exibeAviso('Lista de recursos vazia','n_incluir','aviso');
            exit();
        }
        try {
            RLDOManterReceita::recuperarInstancia()->alterar($arParametros);
            sistemaLegado::alertaAviso('LSManterReceita.php?stAcao=incluir',$arParametros['inNumReceita'],'incluir','aviso');
        } catch (RLDOExcecao $erro) {
            sistemaLegado::exibeAviso($erro->getMessage(), 'n_incluir', 'erro',Sessao::getId(), "../");
        }
    }

    public function recuperarRecurso(array $arParametros)
    {
        return RLDOManterReceita::recuperarInstancia()->recuperarRecurso($arParametros);
    }

    public function listarRecurso(array $arParametros)
    {
        $arRecursos = RLDOManterReceita::recuperarInstancia()->recuperarRecursosReceita($arParametros['inCodReceitaDados']);

        $cont = 0;
        # Formata campo numérico valor.
        foreach ($arRecursos->arElementos as $arCampos) {
            $arParametro['inCodRecurso']                = $arCampos['cod_recurso'];
            $arParametro['inCodReceita']                = $arParametros['inNumReceita'];
            $arRecursosLista['arCodRecurso'][$cont]      = $arCampos['cod_recurso'];
            $arRecurso = RLDOManterReceita::recuperarInstancia()->recuperarRecurso($arParametro);
            $arRecursosLista['arNumRecurso'][$cont]      = $arRecurso->getCampo('nom_recurso');
            $arRecursosLista['arValorRecurso'][$cont]    = $arCampos['valor'];
            $cont++;
        }
        $arRecursosLista['inSizeRecursos'] = $cont;
        $arRecursosLista['hidden'] = $cont;
        $arRecursosLista['inNumReceita'] = $arParametros['inNumReceita'];
        $stJs = $this->inserirRecurso($arRecursosLista);
        $arReceita = RLDOManterReceita::recuperarInstancia()->recuperaReceitaPPA( $arParametros['inNumReceita']);
        $stJs.= "document.getElementById('lbTotalReceita').value = '".$arReceita->getCampo('lbTotalReceita')."';";
        sistemaLegado::executaFrameOculto($stJs);
    }

    public function inserirRecurso(array $arParametros)
    {
        $arCabecalhos = array(
            array('cabecalho' => 'Código',               'width' => 5),
            array('cabecalho' => 'Descrição do Recurso', 'width' => 30),
            array('cabecalho' => 'Valor',                'width' => 15),
        );

        $arComponentes = array(
            array('tipo'  => 'Label', 'name'  => 'arCodRecurso',    'campo' => 'stCodRecurso', 'alinhamento'=>'CENTRO'),
            array('tipo'  => 'Label', 'name'  => 'arNumRecurso',    'campo' => 'stNomRecurso'),
            array('tipo'  => 'Label', 'name'  => 'arValorRecurso', 'campo' => 'flValorRecurso','alinhamento'=>'DIREITA'),
        );

        $arValores = array();
        $arRecurso = RLDOManterReceita::recuperarInstancia()->recuperarRecurso($arParametros);

        if ($arParametros['inNumRecurso']) {
            $arValores[] = array(
                'stCodRecurso'   => $_REQUEST['inNumRecurso'],
                'stNomRecurso'   => $arRecurso->getCampo('nom_recurso'),
                'flValorRecurso' => $_REQUEST['flValorRecurso']
            );
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

        $stHTML = LDOLista::montarLista('Recursos','Lista de Recursos vinculados', $arCabecalhos, $arComponentes, $arValores);
        $stHTML = '$(\'spnListaRecurso\').innerHTML = \'' . $stHTML . '\';';
        $stHTML.= "document.getElementById('inNumRecurso').value = '';";
        $stHTML.= "document.getElementById('flValorRecurso').value = '';";
        $stHTML.= "document.getElementById('stNomRecurso').innerHTML = '&nbsp;';";
        $stHTML.= "document.getElementById('inCodReceitaLista').value = '".$arParametros['inNumReceita']."';";
       // $stHTML.= "d.getElementById('inNumReceita').disabled=true;\n";
        return $stHTML;
    }

    public function exibirRecurso(array $arParametros)
    {

        if (!$arParametros['inNumReceita']) {
            $stJS = "document.getElementById('spnRecurso').innerHTML = '&nbsp;';";
        } else {
            $stJS =  $this->montarRecurso($arParametros['inNumReceita']);

        }

        return $stJS;
    }

    public function recuperarReceitaLDO(array $arParametros)
    {
        $arReceita = RLDOManterReceita::recuperarInstancia()->recuperarReceitaLDO($arParametros);

        return $arReceita;
    }

    public function recuperarReceita(array $arParametros)
    {
        $inAnoVigente = sessao::read('exercicio')+1;

        $stCriterio .= " WHERE ppa.ano_inicio >=".$inAnoVigente." AND ppa.ano_final <= ".$inAnoVigente."\n";
        //if ($arParametros['inCodReceita']
        if ($_REQUEST['inDescricaoReceita']) {
            $stCriterio .= " AND LIKE '%".$_REQUEST['inDescricaoReceita']."%'";
        }

        $obRPPAManterReceita = new RPPAManterReceita();
        $stCriterio .= " AND PR.ativo = 't'                                 \n";
        $stGroupBy  = " GROUP BY PR.cod_receita,                            \n";
        $stGroupBy .= "          PR.cod_ppa,                                \n";
        $stGroupBy .= "          PR.exercicio,                              \n";
        $stGroupBy .= "          PR.cod_conta,                              \n";
        $stGroupBy .= "          PR.cod_entidade,                           \n";
        $stGroupBy .= "          PR.valor_total,                            \n";
        $stGroupBy .= "          ppa.ano_inicio,                            \n";
        $stGroupBy .= "          ppa.ano_final,                             \n";
        $stGroupBy .= "          ppa.destinacao_recurso,                    \n";
        $stGroupBy .= "          OCR.descricao,                             \n";
        $stGroupBy .= "          PN.cod_norma,                              \n";
        $stGroupBy .= "          CGM.nom_cgm                                \n";
        $stCriterio .= $stGroupBy;

        $stOrdem     = ' ORDER BY PR.cod_conta';
        $rsReceita = $obRPPAManterReceita->pesquisar("TPPAReceita","recuperaListaReceitas",$stCriterio);

        return $rsReceita;
    }

    public function montarRecurso($inCodReceita)
    {
        $obForm = new Form();
        $obFormulario = new Formulario();
        $obFormulario->addTitulo('Dados para cadadastro de fontes de Recurso');
        $obIPopUpRecurso = new IPopUpRecurso($obForm);
        $obIPopUpRecurso->obInnerRecurso->setRotulo("*Recurso");
        $obIPopUpRecurso->setCodReceita($inCodReceita);
        $obIPopUpRecurso->geraFormulario($obFormulario);

        //Informar Valor recurso
        $obTxtValorRecurso	 = new Numerico;
        $obTxtValorRecurso->setRotulo    ('*Valor Recurso');
        $obTxtValorRecurso->setTitle     ('Valor Recurso');
        $obTxtValorRecurso->setName      ('flValorRecurso');
        $obTxtValorRecurso->setId        ('flValorRecurso');
        $obTxtValorRecurso->setDecimais  (2);
        $obTxtValorRecurso->setMaxValue  (999999999999.99);
        $obTxtValorRecurso->setNull      (true);
        $obTxtValorRecurso->setNegativo  (false);
        $obTxtValorRecurso->setNaoZero   (false);
        $obTxtValorRecurso->setSize      (20);
        $obTxtValorRecurso->setMaxLength (12);

        //botoes do CGM servidor
        $obBtnIncluirRecurso = new Button;
        $obBtnIncluirRecurso->setName              ('btnIncluirRecurso');
        $obBtnIncluirRecurso->setValue             ('Incluir');
        $obBtnIncluirRecurso->setTipo              ('button');
        $obBtnIncluirRecurso->obEvento->setOnClick ("inserirRecurso();" );
        $obBtnIncluirRecurso->setDisabled          (false);

        $obBtnLimparRecurso = new Button;
        $obBtnLimparRecurso->setName               ('btnLimparRecurso');
        $obBtnLimparRecurso->setValue              ('Limpar');
        $obBtnLimparRecurso->setTipo               ('button');
        $obBtnLimparRecurso->obEvento->setOnClick  ("limparRecurso();");
        $obBtnLimparRecurso->setDisabled           (false);

        $botoesRecurso = array ( $obBtnIncluirRecurso , $obBtnLimparRecurso);

        $obFormulario->addComponente($obTxtValorRecurso);
        $obFormulario->agrupaComponentes($botoesRecurso);

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        return "$('spnRecurso').innerHTML = '" . $stHTML . "';";
    }

}
