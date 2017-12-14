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
    * Classe de visao Manter PPA
    * Data de Criação: 21/09/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Fellipe Esteves dos Santos

    * Casos de uso: uc-02.09.01
*/
require_once CAM_GF_PPA_COMPONENTES . 'IPopUpPrograma.class.php';

class VPPAManterPPA
{
    private $obNegocio;

    public function __construct(RPPAManterPPA $obNegocio)
    {
        $this->obNegocio = $obNegocio;
    }

    public function verificaHomologacao($arParametros)
    {
        if ($_REQUEST['boArredondamento']) {
            $boHomologado = $this->obNegocio->verificaHomologacaoImportacao($arParametros);
            if ($boHomologado == false) {
                $stMensagem  = 'O PPA que vai ser importado não está homologado';
                $stMensagem .= ' Deseja continuar?';

                $stRetorno = "jq_('#stAcao').val('incluir'); jq_('#Ok').trigger('click');";
                // É substituido o jq_ pelo jq padrão para não causar problemas, pois essa confirmPopUp é
                // chamada diretamente e não a partir dos dados do executaFrameOculto
                $stJS = 'confirmPopUp("Aviso", "'.$stMensagem.'", "'.str_replace('jq_', 'jq', $stRetorno).'");';
            } else {
                $stJS = "jq_('#stAcao').val('incluir'); jq_('#Ok').trigger('click');";
            }
        } else {
            $stJS = "jq_('#stAcao').val('incluir'); jq_('#Ok').trigger('click');";
        }

        SistemaLegado::executaFrameOculto($stJS);
    }

    public function incluir($arParametros)
    {
        $this->obNegocio->incluir($arParametros);
    }

    public function excluir($arParametros)
    {
        $this->obNegocio->excluir($arParametros);
    }

    public function listar($arParametros)
    {
        return $this->obNegocio->listar($arParametros);
    }

    public function importar($arParametros)
    {
        $arParametros = array();

        # Obtém ano atual.
        $inExercicio = Sessao::getExercicio();
        $inAnoInicio = $inExercicio + ($inExercicio % 4) + (($inExercicio % 2) ? 4 : 6);
        $arParametros['inAnoInicio'] = $inAnoInicio;

        return $this->obNegocio->importar($arParametros);
    }

    public function pesquisaPPAImportacao($stFiltro = '', $stOrdem = '', $boTrasacao = '')
    {
        return $this->obNegocio->pesquisa('TPPA', 'recuperaPPAImportacao', $stFiltro, $stOrdem, $boTrasacao);
    }

    public function montaSpanPrecisao($arParametros)
    {
        $obFormulario = new Formulario();
        $rsPrecisoes  = $this->obNegocio->pesquisaPrecisoes();

        # Preenche componente com as Precisões.
        $obSelPrecisao = new Select();
        $obSelPrecisao->setId('inCodPrecisao');
        $obSelPrecisao->setName('inCodPrecisao');
        $obSelPrecisao->setRotulo('Nível Arredondamento');
        $obSelPrecisao->setCampoID('cod_precisao');
        $obSelPrecisao->setCampoDesc('nivel');
        $obSelPrecisao->preencheCombo($rsPrecisoes);
        $obSelPrecisao->addOption('', 'Selecione');
        $obSelPrecisao->setNULL(false);
        $obSelPrecisao->setValue(2);
        $obSelPrecisao->setLabel(true);

        # Renderiza componente.
        $obFormulario->addComponente($obSelPrecisao);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        return "$('spnPrecisao').innerHTML = '" . $stHTML . "';";
    }

    public function apagaSpanPrecisao($arParametros)
    {
        return "$('spnPrecisao').innerHTML = '';";
    }

    public function montaSpanPrograma($arParametros)
    {
        $obForm = new Form();
        $obForm->setAction($pgList);
        $obForm->setTarget('telaPrincipal');
        $obFormulario = new Formulario();
        $obIPopUpPrograma = new IPopUpPrograma($obForm);
        $obIPopUpPrograma->setCodPPA($arParametros['inCodPPA']);
        if ($arParametros['boProgramaObrigatorio'] == '1') {
            $obIPopUpPrograma->obInnerPrograma->setRotulo('*Programa');
        }
        $obIPopUpPrograma->geraFormulario($obFormulario);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        return $stHTML;
    }

    private function apagaSpanImportarPPA()
    {
        return '$("spnImportarPPA").innerHTML = "";';
    }

    private function montaSpanImportarPPA()
    {
        $obRdImportarValorT = new Radio;
        $obRdImportarValorT->setRotulo            ( "Importar valor das ações" );
        $obRdImportarValorT->setName              ( "boImportarValorAcao" );
        $obRdImportarValorT->setValue             ( "true" );
        $obRdImportarValorT->setChecked           ( true );
        $obRdImportarValorT->setLabel             ( "Sim" );

        $obRdImportarValorF = new Radio;
        $obRdImportarValorF->setName              ( "boImportarValorAcao" );
        $obRdImportarValorF->setValue             ( "false" );
        $obRdImportarValorF->setChecked           ( false );
        $obRdImportarValorF->setLabel             ( "Não" );

        $obFormulario = new Formulario();
        $obFormulario->agrupaComponentes(array($obRdImportarValorT, $obRdImportarValorF));
        $obFormulario->montaInnerHTML();

        return "$('spnImportarPPA').innerHTML = '" . $obFormulario->getHTML() . "';";
    }

    public function atualizaPPA(array $arParametros)
    {
        $inAnoInicio = (int) $arParametros['stAnoInicio'];
        $inAnoFinal = (int) $arParametros['stAnoInicio'] + 3;
        $stJS = '';

        if (($inAnoInicio % 4 == 2) && ($inAnoInicio >= 2006)) {
            $stJS .= '$("lbAnoFinal").innerHTML = "' . ($inAnoInicio + 3) . '";';
            $stJS .= '$("stAnoFinal").value = "' . ($inAnoInicio + 3) . '";';

            # Testa se o PPA já existe.
            $stFiltro = " ppa.ano_inicio = '" . $inAnoInicio . "' ";
            $stOrdem  = ' ppa.ano_inicio DESC';
            $rsPPA = $this->obNegocio->pesquisa('TPPA', 'recuperaDadosPPA', $stFiltro, $stOrdem, $boTransacao);

            if (!$rsPPA->eof()) {
                $stJS .= "$('stAnoInicio').value = '';";
                $stJS .= "$('stAnoInicio').focus();";
                $stJS .= '$("lbAnoFinal").innerHTML = "";';
                $stJS .= '$("stAnoFinal").value = "";';
                $stJS .= 'alertaAviso("O PPA do período especificado já existe! (';
                $stJS .= $inAnoInicio . ' a ' . $inAnoFinal . ')", "form", "erro", "'.Sessao::getId().'");';

                return $stJS;
            }

            # Testa se o PPA anterior está homologado.
            $stFiltro = " ppa.ano_inicio = '" . ($inAnoInicio - 4) . "' ";
            $stOrdem  = ' ppa.ano_inicio DESC';
            $rsPPA = $this->obNegocio->pesquisa('TPPA', 'recuperaDadosPPA', $stFiltro, $stOrdem, $boTransacao);

            if ($rsPPA->eof()) {
                $stJS .= $this->apagaSpanImportarPPA();
            } else {
                $stJS .= $this->montaSpanImportarPPA();
            }
        } else {
            $stJS .= "$('stAnoInicio').value = '';";
            $stJS .= "$('stAnoInicio').focus();";
            $stJS .= '$("lbAnoFinal").innerHTML = "";';
            $stJS .= '$("stAnoFinal").value = "";';
            $stJS .= 'alertaAviso("O período do PPA deve seguir o período de mandato dos municípios! (';
            $stJS .= $inAnoInicio . ' a ' . $inAnoFinal . ')", "form", "erro", "'.Sessao::getId().'");';
            $stJS .= $this->apagaSpanImportarPPA();
        }

        return $stJS;
    }
}
