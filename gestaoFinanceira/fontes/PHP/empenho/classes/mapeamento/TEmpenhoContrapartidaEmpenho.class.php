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
    * Classe de mapeamento da tabela empenho.contrapartida_empenho
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: luciano $
    $Date: 2007-07-20 17:32:56 -0300 (Sex, 20 Jul 2007) $

    * Casos de uso: uc-02.03.31, uc-02.03.32
*/

/*
$Log$
Revision 1.4  2007/07/20 20:32:56  luciano
Bug#9683#

Revision 1.3  2007/07/16 15:48:46  luciano
Bug#9657#

Revision 1.2  2007/06/27 20:28:58  luciano
Bug#9093#

Revision 1.1  2007/06/27 19:58:02  luciano
Bug#9104#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoContrapartidaEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoContrapartidaEmpenho()
{
    parent::Persistente();
    $this->setTabela("empenho.contrapartida_empenho");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_empenho,cod_entidade,exercicio,conta_contrapartida');

    $this->AddCampo('cod_empenho'        ,'INTEGER'   ,true,'',true,false);
    $this->AddCampo('cod_entidade'       ,'INTEGER'   ,true,'',true,false);
    $this->AddCampo('exercicio'          ,'VARCHAR(4)',true,'',true,true);
    $this->AddCampo('conta_contrapartida','INTEGER'   ,true,'',true,true);

}

    public function recuperaContrapartidaLancamento(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
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
        $stSQL .= "             ecp.conta_contrapartida                                  \n";
        $stSQL .= "            ,cpc.nom_conta                                            \n";
        $stSQL .= "        FROM empenho.contrapartida_empenho as ecp                     \n";
        $stSQL .= "             JOIN contabilidade.plano_analitica as cpa ON (           \n";
        $stSQL .= "                    cpa.cod_plano = ecp.conta_contrapartida           \n";
        $stSQL .= "                AND cpa.exercicio = ecp.exercicio                     \n";
        $stSQL .= "             )                                                        \n";
        $stSQL .= "             JOIN contabilidade.plano_conta as cpc ON (               \n";
        $stSQL .= "                    cpc.cod_conta = cpa.cod_conta                     \n";
        $stSQL .= "                AND cpc.exercicio = cpa.exercicio                     \n";
        $stSQL .= "             )                                                        \n";
        $stSQL .= "        WHERE   ecp.exercicio = '".$this->getDado('exercicio')."'     \n";
        $stSQL .= "            AND ecp.cod_empenho = ".$this->getDado('cod_empenho')."   \n";
        $stSQL .= "            AND ecp.cod_entidade = ".$this->getDado('cod_entidade')." \n";

        return $stSQL;
    }

}
