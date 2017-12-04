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
 * Classe Mapeameto do 02.10.03 - Manter Ação
 * Data de Criação: 05/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

class TLDOAcaoInativaNorma extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ldo.acao_inativa_norma');

        $this->setCampoCod('cod_acao');

        $this->addCampo('cod_acao', 'integer', true, '', true, true);
        $this->addCampo('cod_norma', 'integer', true, '', false, true);
        $this->addCampo('timestamp', 'timestamp', true, '', false, false);
    }

    public function recuperaNormaAcao(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL = $this->montaRecuperaNormaAcao($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaNormaAcao($stFiltro = '', $stOrdem = '')
    {
        if ($stFiltro != "") {
            $stWhere = " WHERE " . $stFiltro . "";
        }

        $stSql = "       SELECT acao.cod_acao                                                               \n";
        $stSql.= "            , acao_inativa_norma.cod_norma                                                \n";
        $stSql.= "            , acao_inativa_norma.timestamp                                                \n";
        $stSql.= "         FROM ldo.acao_inativa_norma                                                      \n";
        $stSql.= "   INNER JOIN ldo.acao                                                                    \n";
        $stSql.= "           ON acao.cod_acao = acao_inativa_norma.cod_acao                                 \n";

        if ($stOrdem) {
            $stOrderBy = ' ORDER BY ' . $stOrdem;
        }

        return $stSql . $stWhere . $stOrderBy;
    }

}
