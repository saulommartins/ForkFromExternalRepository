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
 * Classe de Visao do 02.10.05 - Manter Ajuste de Anexo
 * Data de Criação: 17/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.05 - Manter Ajuste de Anexo
 */
include_once CAM_GF_LDO_VISAO   . 'VLDOPadrao.class.php';
include_once CAM_GF_LDO_NEGOCIO . 'RLDOManterNotaExplicativa.class.php';

class VLDOManterNotaExplicativa extends VLDOPadrao implements IVLDOPadrao
{
    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    public function inicializar()
    {
        parent::inicializarRegra(__CLASS__);
    }

    /**
     * Inclui um Ajuste de Anexo
     *
     * @param array $arParametros
     */
    public function incluir(array $arParametros)
    {
        try {
            $inCodNotaExplicativa = $this->recuperarRegra()->incluir($arParametros);
            $stCaminho = 'FMManterNotaExplicativa.php?stAcao=incluir';
            SistemaLegado::alertaAviso($stCaminho, $inCodNotaExplicativa, 'incluir', 'aviso', Sessao::getID(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_incluir', 'erro');
        }
    }

    public function excluir(array $arParametros)
    {
        try {
            $inCodNotaExplicativa = $this->recuperarRegra()->excluir($arParametros);
            $stCaminho = 'LSManterNotaExplicativa.php?stAcao=excluir';
            SistemaLegado::alertaAviso($stCaminho, $inCodNotaExplicativa, 'excluir', 'aviso', Sessao::getID(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_alterar', 'erro');
        }
    }

    public function alterar(array $arParametros)
    {
        try {
            $inCodNotaExplicativa = $this->recuperarRegra()->alterar($arParametros);
            $stCaminho = 'LSManterNotaExplicativa.php?stAcao=alterar';
            SistemaLegado::alertaAviso($stCaminho, $inCodNotaExplicativa, 'alterar', 'aviso', Sessao::getID(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso('OH NOES! ' . $e->getMessage(), 'n_alterar', 'erro');
        }
    }

    /**
     * Recupera lista de Notas Explicativas
     *
     * @return RecordSet
     */
    public function recuperarListaNotaExplicativa()
    {
        $rsListaNotaExplicativa = RLDOManterNotaExplicativa::recuperarInstancia()->recuperarListaNotaExplicativa();

        return $rsListaNotaExplicativa;
    }

    /**
     * Recupera dados da Nota Explicativa
     *
     * @param  array $arParametros
     * @return array Array atualizado com os demais dados da Nota Explicativa
     */
    public function recuperarDados(array &$arParametros)
    {
        if (!$this->recuperarRegra()->recuperarDados($arParametros)) {
            throw new VLDOExcecao('Erro ao tentar obter dados da Nota Explicativa.', $this->recuperarAnotacoes());
        }
    }

}
