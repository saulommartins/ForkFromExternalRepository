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
    * Classe de mapeamento da funçao AplicaJuroReemissao
    * Data de Criação:

    * @author Script: Eduardo Paculski Schitz
    * @package URBEM

    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';

class Faplica_juro_reemissao extends Persistente
{

    public function Faplica_juro_reemissao()
    {
        parent::Persistente();
        $this->AddCampo('valor', 'numeric', false, '', false, false);
    }

    public function executaFuncao(&$rsRecordset, $stParametros, $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql  = $this->montaExecutaFuncao($stParametros);
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordset, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaExecutaFuncao($stParametros)
    {
        $stSql  = " SELECT aplica_juro_reemissao(".$stParametros.") as valor \r\n";

        return $stSql;
    }
}
?>
