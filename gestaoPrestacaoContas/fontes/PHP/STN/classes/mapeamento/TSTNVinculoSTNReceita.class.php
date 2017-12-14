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
    * Classe de mapeamento da tabela STN.VINCULO_RECURSO
    * Data de Criação: 08/05/2008

    * @author Analista: Tonismar Regis Bernardo
    * @author Desenvolvedor: Leopoldo Braga Barreiro

    * $Id:$

    * Casos de uso:

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TSTNVinculoSTNReceita extends Persistente
{

    /**
        * Método Construtor
    */
    public function TSTNVinculoSTNReceita()
    {

        parent::Persistente();

        $this->setTabela('stn.vinculo_stn_receita');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_receita, cod_tipo');

        $this->AddCampo('exercicio'  , 'char'   , true, '04', true, true);
        $this->AddCampo('cod_receita', 'integer', true,   '', true, true);
        $this->AddCampo('cod_tipo'   , 'integer', true,   '', true, true);

    }

    /**
     * Método que retorna as receitas do anexo 3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listReceitas(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT receita.cod_receita
                 , receita.exercicio
                 , conta_receita.cod_estrutural
                 , conta_receita.descricao
                 , tipo_vinculo_stn_receita.cod_tipo
                 , tipo_vinculo_stn_receita.descricao AS nom_tipo
              FROM stn.vinculo_stn_receita
        INNER JOIN orcamento.receita
                ON vinculo_stn_receita.exercicio   = receita.exercicio
               AND vinculo_stn_receita.cod_receita = receita.cod_receita
        INNER JOIN orcamento.conta_receita
                ON receita.exercicio = conta_receita.exercicio
               AND receita.cod_conta = conta_receita.cod_conta
        INNER JOIN stn.tipo_vinculo_stn_receita
                ON vinculo_stn_receita.cod_tipo = tipo_vinculo_stn_receita.cod_tipo
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
}

?>
