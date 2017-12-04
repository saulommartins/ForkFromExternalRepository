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
    * Classe de mapeamento da tabela ima.configuracao_dirf_prestador
    * Data de Criação: 16/01/2009

    * @author Analista     : Dagiane
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TIMAConfiguracaoDirfPrestador extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TIMAConfiguracaoDirfPrestador()
    {
        parent::Persistente();
        $this->setTabela("ima.configuracao_dirf_prestador");

        $this->setCampoCod('cod_prestador');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('exercicio'    ,'char'    ,true  ,'4'  ,true,'TOrcamentoContaDespesa');
        $this->AddCampo('cod_prestador','sequence',true  ,''   ,true,false);
        $this->AddCampo('cod_dirf'     ,'integer' ,true  ,''   ,false,'TIMACodigoDirf');
        $this->AddCampo('tipo'         ,'char'    ,true  ,'1'  ,false,'TIMACodigoDirf');
        $this->AddCampo('cod_conta'    ,'integer' ,true  ,''   ,false,'TOrcamentoContaDespesa');
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "        SELECT *                                                                 \n";
        $stSql .= "             , ( CASE WHEN configuracao_dirf_prestador.tipo = 'F'                \n";
        $stSql .= "                        THEN 'Pessoa Física'                                     \n";
        $stSql .= "                        ELSE 'Pessoa Jurídica'                                   \n";
        $stSql .= "                   END ) as tipo_formatado                                       \n";
        $stSql .= "             , conta_despesa.descricao as descricao_conta_despesa                \n";
        $stSql .= "             , codigo_dirf.descricao as descricao_codigo_dirf                    \n";
        $stSql .= "          FROM ima.configuracao_dirf_prestador                                   \n";
        $stSql .= "    INNER JOIN orcamento.conta_despesa                                           \n";
        $stSql .= "            ON configuracao_dirf_prestador.exercicio = conta_despesa.exercicio   \n";
        $stSql .= "           AND configuracao_dirf_prestador.cod_conta = conta_despesa.cod_conta   \n";
        $stSql .= "    INNER JOIN ima.codigo_dirf                                                   \n";
        $stSql .= "       ON configuracao_dirf_prestador.exercicio = codigo_dirf.exercicio          \n";
        $stSql .= "      AND configuracao_dirf_prestador.cod_dirf = codigo_dirf.cod_dirf            \n";
        $stSql .= "      AND configuracao_dirf_prestador.tipo = codigo_dirf.tipo                    \n";

        return $stSql;
    }
}
?>
