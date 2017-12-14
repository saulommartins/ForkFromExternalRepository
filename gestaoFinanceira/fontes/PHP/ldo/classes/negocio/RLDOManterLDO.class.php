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
 * Classe Negocio do 02.10.00 - Manter LDO
 * Data de Criação: 06/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.00 - Manter LDO
 */

include_once CAM_GF_LDO_NEGOCIO    . 'RLDOPadrao.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDO.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';

class RLDOManterLDO extends RLDOPadrao implements IRLDOPadrao
{
    private $obTLDO;
    private $obTPPA;

    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    protected function inicializar()
    {
        $this->obTLDO = new TLDO();
        $this->obTPPA = new TPPA();
    }

    public function recuperarLDO($stAnoLDO = null)
    {
        if (!$stAnoLDO) {
            $stAnoLDO = Sessao::read('exercicio') + 1;
        }

        $stFiltro = " WHERE ano = $stAnoLDO";

        $obErro = $this->obTLDO->recuperaTodos($rsLDO, $stFiltro);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar obter o LDO.');
        }

        if ($rsLDO->eof()) {
            throw new RLDOExcecao('Não existe PPA homologado para o ano ('.$stAnoLDO.').');
        }

        return $rsLDO;
    }

    public function recuperarLDOHomologado($stAnoLDO = null)
    {
        if (!$stAnoLDO) {
            $stAnoLDO = Sessao::read('exercicio') + 1;
        }

        $stFiltro = " WHERE ldo.ano = $stAnoLDO";

        $obErro = $this->obTLDO->recuperaLDOHomologado($rsLDO, $stFiltro);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar obter o LDO homologado.');
        }

        if ($rsLDO->eof()) {
            return false;
        }

        return true;
    }

    public function recuperarPPA($inCodPPA)
    {
        if (!$inCodPPA) {
            $rsLDO    = $this->recuperarLDO();
            $inCodPPA = $rsLDO->getCampo('cod_ppa');
        }

        $stFiltro = " WHERE cod_ppa = $inCodPPA";

        $obErro = $this->obTPPA->recuperaTodos($rsPPA, $stFiltro);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar obter o PPA.');
        }

        if ($rsPPA->eof()) {
            throw new RLDOExcecao('Não foi encontrado nenhum PPA cadastrado.');
        }

        return $rsPPA;
    }

    public function incluir(array $arArgs)
    {
    }

    public function alterar(array $arArgs)
    {
    }

    public function excluir(array $arArgs)
    {
    }

}
