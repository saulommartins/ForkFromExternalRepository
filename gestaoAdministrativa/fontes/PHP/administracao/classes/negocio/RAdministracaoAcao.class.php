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
 * Classe de negócio Acao
 * Data de Criação: 25/07/2005
 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 $Id: RAdministracaoAcao.class.php 59612 2014-09-02 12:00:51Z gelson $

 Casos de uso: uc-01.03.91
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO . "TAdministracaoAcao.class.php");

class RAdministracaoAcao
{
    public $inCodigoAcao;
    public $stNomeAcao;
    public $stNomeArquivo;
    public $stParametro;
    public $inOrdem;
    public $stComplementoAcao;
    public $roRAdministracaoFuncionalidade;

    public function RAdministracaoAcao()
    {

    }

    public function setCodigoAcao($valor)
    {
        $this->inCodigoAcao = $valor;

    }

    public function setNomeAcao($valor)
    {
        $this->stNomeAcao = $valor;

    }

    public function setNomeArquivo($valor)
    {
        $this->stNomeArquivo = $valor;

    }

    public function setParametro($valor)
    {
        $this->stParametro = $valor;

    }

    public function setOrdem($valor)
    {
        $this->inOrdem = $valor;

    }

    public function setComplementoAcao($valor)
    {
        $this->stComplementoAcao = $valor;

    }

    public function setRAdministracaoFuncionalidade(&$valor)
    {
        $this->roRAdministracaoFuncionalidade = &$valor;

    }

    public function getCodigoAcao()
    {
        return $this->inCodigoAcao;

    }

    public function getNomeAcao()
    {
        return $this->stNomeAcao;

    }

    public function getNomeArquivo()
    {
        return $this->stNomeArquivo;

    }

    public function getParametro()
    {
        return $this->stParametro;

    }

    public function getOrdem()
    {
        return $this->inOrdem;

    }

    public function getComplementoAcao()
    {
        return $this->stComplementoAcao;

    }

    public function getRAdministracaoFuncionalidade()
    {
        return $this->roRAdministracaoFuncionalidade;

    }

    public function listar(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {

        if (isset($this->inCodigoAcao) && empty($stFiltro)) {

            $stFiltro = ' and cod_acao = ' . $this->getCodigoAcao();

        }
        $obTAdministracaoAcao = new TAdministracaoAcao;
        $obErro = $obTAdministracaoAcao->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;

    }

    public function consultarAcao($boTransacao = '')
    {
        $obErro = new Erro;
        $obTAdministracaoAcao = new TAdministracaoAcao();
        $obTAdministracaoAcao->setDado('cod_acao', $this->getCodigoAcao());
        $obErro = $obTAdministracaoAcao->recuperaPorChave($rsAcao, $boTransacao);
        if (!$obErro->ocorreu() and !$rsAcao->eof()) {

            $this->setNomeAcao($rsAcao->getCampo('nom_acao'));
            $this->setNomeArquivo($rsAcao->getCampo('nom_arquivo'));
            $this->setParametro($rsAcao->getCampo('parametro'));
            $this->setOrdem($rsAcao->getCampo('ordem'));
            $this->setComplementoAcao($rsAcao->getCampo('complemento_acao'));

        }

        return $obErro;

    }

}
