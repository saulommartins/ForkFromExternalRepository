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
    * Classe de mapeamento da tabela contrapartida_autorizacao
    * Data de Criação: 04/09/2007

    * @author Analista: Valtair
    * @author Desenvolvedor: Luciano Hoffmann

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: luciano $
    $Date: 2007-09-06 17:58:13 -0300 (Qui, 06 Set 2007) $

    * Casos de uso: uc-02-03-02, uc-02.03.31, uc-02.03.32
*/

/*
$Log$
Revision 1.1  2007/09/06 20:58:13  luciano
Adicionada ao repositorio
Ticket#9094#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoContrapartidaAutorizacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoContrapartidaAutorizacao()
{
    parent::Persistente();
    $this->setTabela("empenho.contrapartida_autorizacao");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_autorizacao,cod_entidade,exercicio');

    $this->AddCampo('cod_autorizacao'    ,'INTEGER'   ,true,'',true,false);
    $this->AddCampo('cod_entidade'       ,'INTEGER'   ,true,'',true,false);
    $this->AddCampo('exercicio'          ,'VARCHAR(4)',true,'',true,true);
    $this->AddCampo('conta_contrapartida','INTEGER'   ,true,'',true,true);

}

function recuperaContrapartidaLancamento(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
         $obErro      = new Erro;
         $obConexao   = new Conexao;
         $rsRecordSet = new RecordSet;
         $stSql = $this->montaRecuperaContrapartidaLancamento().$stCondicao;
         $this->setDebug( $stSql );
         $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

         return $obErro;
    }

    public function montaRecuperaContrapartidaLancamento()
    {
        $stSQL  = "        SELECT                                                        \n";
        $stSQL .= "             eca.conta_contrapartida                                  \n";
        $stSQL .= "            ,cpc.nom_conta                                            \n";
        $stSQL .= "        FROM empenho.contrapartida_autorizacao as eca                 \n";
        $stSQL .= "             JOIN contabilidade.plano_analitica as cpa ON (           \n";
        $stSQL .= "                    cpa.cod_plano = eca.conta_contrapartida           \n";
        $stSQL .= "                AND cpa.exercicio = eca.exercicio                     \n";
        $stSQL .= "             )                                                        \n";
        $stSQL .= "             JOIN contabilidade.plano_conta as cpc ON (               \n";
        $stSQL .= "                    cpc.cod_conta = cpa.cod_conta                     \n";
        $stSQL .= "                AND cpc.exercicio = cpa.exercicio                     \n";
        $stSQL .= "             )                                                        \n";
        $stSQL .= "        WHERE   eca.exercicio = '".$this->getDado('exercicio')."'     \n";
        $stSQL .= "            AND eca.cod_autorizacao = ".$this->getDado('cod_autorizacao')."   \n";
        $stSQL .= "            AND eca.cod_entidade = ".$this->getDado('cod_entidade')." \n";

        return $stSQL;
    }

}
