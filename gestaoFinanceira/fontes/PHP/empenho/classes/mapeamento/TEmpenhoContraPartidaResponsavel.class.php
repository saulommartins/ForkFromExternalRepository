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
    * Classe de mapeamento da tabela EMPENHO.CONTRAPARTIDA_RESPONSAVEL
    * Data de Criação: 17/10/2006

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Rodrigo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.03.32
*/

/*
$Log$
Revision 1.2  2007/06/18 19:53:15  luciano
#9090#

Revision 1.1  2006/10/24 11:01:49  rodrigo
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoContraPartidaResponsavel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
 function TEmpenhoContraPartidaResponsavel()
 {
     parent::Persistente();
     $this->setTabela('empenho.contrapartida_responsavel');

     $this->setCampoCod('conta_contrapartida');
     $this->setComplementoChave('exercicio');

     $this->AddCampo('exercicio','char',true,'4',true,true);
     $this->AddCampo('conta_contrapartida','integer',true,'',true,false);
     $this->AddCampo('prazo','integer',true,'',true,false);
 }

 function recuperaContraPartidaResponsavel(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
 {
     $obErro      = new Erro;
     $obConexao   = new Conexao;
     $rsRecordSet = new RecordSet;
     $stSql = $this->montaContraPartidaResponsavel().$stCondicao;
     $this->setDebug($stSql);
     $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

     return $obErro;
 }

 function montaContraPartidaResponsavel()
 {
   $stSQL = "SELECT contrapartida_responsavel.exercicio                                                             \n";
   $stSQL.= "      ,contrapartida_responsavel.conta_contrapartida                                                   \n";
   $stSQL.= "      ,contrapartida_responsavel.prazo                                                                 \n";
   $stSQL.= "      ,plano_conta.nom_conta AS descricao                                                              \n";
   $stSQL.= "  FROM contabilidade.plano_analitica                                                                   \n";
   $stSQL.= "      ,contabilidade.plano_conta                                                                       \n";
   $stSQL.= "      ,empenho.contrapartida_responsavel                                                               \n";
   $stSQL.= "      ,empenho.responsavel_adiantamento                                                                \n";
   $stSQL.= " WHERE contrapartida_responsavel.conta_contrapartida = plano_analitica.cod_plano                       \n";
   $stSQL.= "   AND contrapartida_responsavel.exercicio           = plano_analitica.exercicio                       \n";
   $stSQL.= "   AND contrapartida_responsavel.conta_contrapartida = responsavel_adiantamento.conta_contrapartida    \n";
   $stSQL.= "   AND contrapartida_responsavel.exercicio           = responsavel_adiantamento.exercicio              \n";
   $stSQL.= "   AND plano_analitica.cod_conta                     = plano_conta.cod_conta                           \n";
   $stSQL.= "   AND plano_analitica.exercicio                     = plano_conta.exercicio                           \n";

   return $stSQL;
 }

}
