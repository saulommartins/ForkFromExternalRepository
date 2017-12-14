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
/*
 * Classe de mapeamento da tabela tceam.esfera_convenio
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMEsferaConvenio extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCEAMEsferaConvenio()
    {
        parent::Persistente();
        $this->setTabela('tceam.esfera_convenio');

        $this->setComplementoChave('num_convenio, exercicio');

        $this->AddCampo('num_convenio', 'integer', true, '' , true , true);
        $this->AddCampo('exercicio'   , 'varchar', true, '4', true , true);
        $this->AddCampo('esfera'      , 'char'   , true, '1', false, true);
    }

    public function recuperaEsferaConvenio(&$rsRecordSet, $stCondicao = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEsferaConvenio().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaEsferaConvenio()
    {
        $stSql = "
            SELECT convenio.num_convenio
                 , convenio.exercicio
                 , objeto.descricao AS descricao_objeto
                 , esfera_convenio.esfera
              FROM licitacao.convenio
              JOIN compras.objeto
                ON objeto.cod_objeto = convenio.cod_objeto
         LEFT JOIN tceam.esfera_convenio
                ON esfera_convenio.num_convenio = convenio.num_convenio
               AND esfera_convenio.exercicio    = convenio.exercicio
          ORDER BY convenio.num_convenio ";

        return $stSql;
    }

}
