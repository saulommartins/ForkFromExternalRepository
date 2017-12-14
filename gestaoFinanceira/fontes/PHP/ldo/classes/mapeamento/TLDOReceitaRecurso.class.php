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
 * Pagina de MAPEAMENTO Receita_recursos tipo do uc-02.10.04
 * Data de Criação: 05/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author <analista> Bruno Ferreira Santos <bruno.ferreira>
 * @author <desenvolvedor> Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage ldo
 * @uc uc-02.10.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TLDOReceitaRecurso extends Persistente
{

    /**
     * Método Construtor
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ldo.receita_recurso');
        $this->setCampoCod('cod_receita');
        $this->setComplementoChave('cod_receita_dados','cod_recursos','exercicio');

        // campo, tipo, not_null, data_length, pk, fk
        $this->AddCampo('cod_receita','integer',true,'',true,true);
        $this->AddCampo('cod_receita_dados','integer',true,'',true,true);
        $this->AddCampo('cod_recurso','integer',true,'',true,true);
        $this->AddCampo('exercicio','char',true,'4',true,true);
        $this->AddCampo('valor','numeric',true,'14,2',false,false);

    }

    public function recuperaDadosRecurso(&$rsRecordSet, $stCriterio, $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql       = $this->montaRecuperaDadosRecurso($stCriterio).$stOrdem;
        $obErro      = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDadosRecurso($stCriterio)
    {
        $stSQL = "   SELECT                                                               \n";
        $stSQL .= "                       receita_recurso.cod_receita                     \n";
        $stSQL .= "                     , receita_recurso.cod_receita_dados               \n";
        $stSQL .= "                     , receita_recurso.cod_recurso                     \n";
        $stSQL .= "                     , receita_recurso.exercicio                       \n";
        $stSQL .= "                     , to_real (receita_recurso.valor) as valor                \n";
        $stSQL .= "                  FROM ldo.receita_recurso                             \n";
        $stSQL .= $stCriterio;

        return $stSQL;
    }

}
