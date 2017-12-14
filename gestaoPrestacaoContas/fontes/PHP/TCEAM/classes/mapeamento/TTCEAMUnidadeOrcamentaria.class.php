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
    * Classe de Mapeamento - Exportação Arquivos
    *
    * Data de Criação: 03/03/2011
    *
    *
    * @author: Tonismar R. Bernardo
    * @ignore
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

class TTCEAMUnidadeOrcamentaria extends Persistente
{
    public function recuperaRelacionamentoUnidadeOrcamentaria(&$rsRecordSet, $stCondicao = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if( trim($stOrdem) )
            $stOrdem = (strpos($stOrdem,'ORDER BY') === false) ? ' ORDER BY $stOrdem' : $stOrdem;
        $stSql = $this->montarecuperaRelacionamentoUnidadeOrcamentaria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoUnidadeOrcamentaria()
    {
        $stSql .= " SELECT unidade.*                                              \n";
        $stSql .= "       ,orgao.nom_orgao                                        \n";
        $stSql .= "       ,orgao.num_orgao                                        \n";
        $stSql .= "       ,lpad(unidade.num_unidade::varchar,2,0::varchar) as num_unidade           \n";
        $stSql .= "       ,orgao.exercicio as exercicio_orgao                     \n";
        $stSql .= "       ,unidade.exercicio as exercicio_unidade                 \n";
        $stSql .= "       ,sw_cgm_pessoa_fisica.cpf                               \n";
        $stSql .= "       ,sw_cgm.nom_cgm                                         \n";
        $stSql .= "   FROM orcamento.unidade                                      \n";
        $stSql .= "       ,orcamento.orgao                                        \n";
        $stSql .= "       ,sw_cgm                                                 \n";
        $stSql .= "       ,sw_cgm_pessoa_fisica                                   \n";
        $stSql .= "  WHERE unidade.exercicio        = orgao.exercicio             \n";
        $stSql .= "    AND unidade.num_orgao        = orgao.num_orgao             \n";
        $stSql .= "    AND orgao.usuario_responsavel = sw_cgm.numcgm              \n";
        $stSql .= "    AND sw_cgm.numcgm            = sw_cgm_pessoa_fisica.numcgm \n";

        return $stSql;
    }
}
