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
 * Classe de Visao do 02.10.06 - Manter Riscos Fiscais
 * Data de Criação: 10/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.06 - Manter Riscos Fiscais
 */
include_once CAM_GF_LDO_VISAO   . 'VLDOPadrao.class.php';
include_once CAM_GF_LDO_NEGOCIO . 'RLDOManterRiscoFiscal.class.php';
include_once CAM_GF_LDO_UTIL    . 'LDOLista.class.php';
include_once CAM_GF_LDO_UTIL    . 'LDOString.class.php';

class VLDOManterRiscoFiscal extends VLDOPadrao implements IVLDOPadrao
{
    /**
     * Retorna uma instancia da classe
     *
     * @return VLDOManterRiscoFiscal
     */
    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    /**
     * Instancia objeto Regra
     */
    public function inicializar()
    {
        parent::inicializarRegra(__CLASS__);
    }

    /**
     * Inclui Risco Fiscal
     *
     * @param  array $arParametros
     * @return void
     */
    public function incluir(array $arParametros)
    {
        if ($this->checarCadastroRiscoFiscal($arParametros['stDescRiscoFiscal']) > 0) {
            $stMsgErro = 'Já existe um Risco Fiscal cadastrado com o mesmo nome!';
            SistemaLegado::LiberaFrames(true,false);

            return SistemaLegado::exibeAviso($stMsgErro,'n_incluir' ,'aviso');
        }
        if (empty($arParametros['inSizeProvidencia'])) {
            SistemaLegado::LiberaFrames(true,false);

            return SistemaLegado::exibeAviso('Lista de Dados da Providência vazia','n_incluir','aviso');
        }
        if (!$this->compararValores($arParametros)) {
            SistemaLegado::LiberaFrames(true,false);
            $stMsgErro = 'Os valores de Riscos Fiscais e Providência devem ser iguais!';

            return SistemaLegado::exibeAviso($stMsgErro,'n_incluir' ,'aviso');
        }
        try {
            $inCodRiscoFiscal = $this->recuperarRegra()->incluir($arParametros);
            SistemaLegado::alertaAviso('FMManterRiscoFiscal.php?stAcao=incluir', $inCodRiscoFiscal .' - '. $arParametros['stDescRiscoFiscal'], 'incluir', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso($e->getMessage(), 'n_incluir', 'erro');
        }
    }

    /**
     * Altera um Risco Fiscal
     *
     * @param  array $arParametros
     * @return void
     */
    public function alterar(array $arParametros)
    {
        if (empty($arParametros['inSizeProvidencia'])) {
            return SistemaLegado::exibeAviso('Lista de Dados da Providência vazia','n_alterar','aviso');
        }
        if (!$this->compararValores($arParametros)) {
            $stMsgErro = 'Os valores de Riscos Fiscais e Providência devem ser iguais!';

            return SistemaLegado::exibeAviso($stMsgErro,'n_alterar' ,'aviso');
        }
        try {
            $this->recuperarRegra()->alterar($arParametros);
            SistemaLegado::alertaAviso('LSManterRiscoFiscal.php?stAcao=alterar', $arParametros['inCodRiscoFiscal'] .' - '. $arParametros['stDescRiscoFiscal'], 'alterar', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_alterar', 'erro');
        }
    }

    /**
     * Exclui um Risco Fiscal de suas respectivas
     * Providências Fiscais
     *
     * @param  array $arParametros
     * @return void
     */
    public function excluir(array $arParametros)
    {
        try {
            $this->recuperarRegra()->excluir($arParametros);
            SistemaLegado::alertaAviso('LSManterRiscoFiscal.php?stAcao=excluir&inCodRiscoFiscal=' . $arParametros['inCodRiscoFiscal'], $arParametros['inCodRiscoFiscal'] .' - ' . $arParametros['stDescQuestao'], 'excluir', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_excluir', 'erro', Sessao::getId(), '../');
        }
    }

    public function recuperarRiscoFiscal($inAnoLDO)
    {
        return $this->recuperarRegra()->recuperarRiscoFiscal($inAnoLDO);
    }

    /**
     * Monta a lista de Providências com os valores
     * armazenados no banco de dados.
     */
    public function recuperarProvidenciaFiscal(array $arParametros)
    {
        $rsProvidenciaFiscal = RLDOManterRiscoFiscal::recuperarInstancia()->recuperarProvidenciaFiscal($arParametros['inCodRiscoFiscal']);

        return $this->inserirProvidencia($arParametros, $rsProvidenciaFiscal);
    }

    /**
     * Insere dados na lista de Providências
     *
     * @param  array     $arParametros
     * @param  RecordSet $rsProvidenciaFiscal
     * @return string    HTML lista
     */
    public function inserirProvidencia(array $arParametros, $rsProvidenciaFiscal = null)
    {

        $stJS = '';

        $arCabecalhos = array(
            array('cabecalho' => 'Providência', 'width' => 100),
            array('cabecalho' => 'Valor',       'width' => 10)
        );

        $arComponentes = array(
            array('tipo'  => 'Label', 'name'  => 'arDescProvidencia',  'campo' => 'stDescProvidencia'),
            array('tipo'  => 'Label', 'name'  => 'arValorProvidencia', 'campo' => 'flValorProvidencia', 'alinhamento' => 'DIREITA')
        );

        $arValores = array();

        if (isset($rsProvidenciaFiscal)) {
            $flValorTotal = 0;
            for ($i = 0; $i < $rsProvidenciaFiscal->inNumLinhas; $i++) {
                $stValorMonetario = LDOString::retornarValorMonetario($rsProvidenciaFiscal->arElementos[$i]['valor']);
                $arValores[] = array(
                    'stDescProvidencia'  => stripslashes($rsProvidenciaFiscal->arElementos[$i]['descricao']),
                    'flValorProvidencia' => $stValorMonetario
                );
                $flValorTotal += $rsProvidenciaFiscal->arElementos[$i]['valor'];
            }

            $stJS .= "$('lbTotalProvidencia').innerHTML = retornaFormatoMonetario(" . $flValorTotal . ");";
        } else {

            if ($arParametros['stDescProvidencia']) {
                $arValores[] = array(
                    'stDescProvidencia'  => stripslashes($arParametros['stDescProvidencia']),
                    'flValorProvidencia' => $arParametros['flValorProvidencia']
                );
            }

            if (count($arParametros['arDescProvidencia'])) {
                foreach ($arParametros['arDescProvidencia'] as $inChave => $inValor) {
                    $arLinha = array();
                    foreach ($arComponentes as $arCampos) {
                        $arLinha[$arCampos['campo']] = stripslashes($arParametros[$arCampos['name']][$inChave]);
                    }
                    array_unshift($arValores, $arLinha);
                }
            }

        }

        $stHTML  = LDOLista::montarLista('Providencia','Lista de Providências', $arCabecalhos, $arComponentes, $arValores);
        $stJS .= "document.getElementById('spnListaProvidencia').innerHTML = '".$stHTML."';";
        $stJS .= "document.getElementById('flValorProvidencia').value = '';";
        $stJS .= "document.getElementById('stDescProvidencia').value = '';";

        return $stJS;
    }

    /**
     * Compara o valor do Risco Fiscal com o valor total das
     * Providências Fiscais.
     *
     * @param  array $arParametros
     * @return bool
     */
    private function compararValores(array $arParametros)
    {
        $flValorRiscoFiscal = LDOString::retornarValorFloat($arParametros['flValorRiscoFiscal']);
        $flValorTotalProvidencia = 0;
        for ($i = 0; $i < $arParametros['inSizeProvidencia']; $i++) {
            $flValorProvidencia = LDOString::retornarValorFloat($arParametros['arValorProvidencia'][$i]);
            $flValorTotalProvidencia += $flValorProvidencia;
        }
        if ($flValorRiscoFiscal != $flValorTotalProvidencia) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se já existe ums Risco Fiscal cadastrado com o mesmo nome.
     *
     * @param  array $arParametros
     * @return int   Número registros encontrados
     */
    public function checarCadastroRiscoFiscal($stDescRiscoFiscal)
    {
        $stDescOriginal = trim($stDescRiscoFiscal);
        $stDescTratada  = LDOString::retirarAcento($stDescOriginal);
        $numRegistros = $this->recuperarRegra()->checarCadastroRiscoFiscal($stDescOriginal, $stDescTratada);

        return $numRegistros;
    }

}
