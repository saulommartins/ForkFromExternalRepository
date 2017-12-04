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
    * Classe de mapeamento da tabela PESSOAL.FAIXA_TURNO
    * Data de Criação: 13/09/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.FAIXA_TURNO
  * Data de Criação: 13/09/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalFaixaTurno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalFaixaTurno()
{
    parent::Persistente();
    $this->setTabela('pessoal.faixa_turno');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_turno,cod_grade,timestamp');

    $this->AddCampo('cod_turno','integer',true,'',true,false);
    $this->AddCampo('cod_grade','integer',true,'',true,true);
    $this->AddCampo('hora_entrada','time',true,'',false,false);
    $this->AddCampo('hora_saida','time',true,'',false,false);
    $this->AddCampo('hora_entrada_2','time',false,'',false,false);
    $this->AddCampo('hora_saida_2','time',false,'',false,false);
    $this->AddCampo('cod_dia','integer',true,'',false,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
}

function recuperaGrade(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaGrade().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaGrade()
{
    $stSQL .="SELECT pt.cod_turno                                          \n";
    $stSQL .="     , pt.cod_grade                                          \n";
    $stSQL .="     , to_char(pt.hora_entrada,'hh24:mi') as hora_entrada    \n";
    $stSQL .="     , to_char(pt.hora_saida,'hh24:mi') as hora_saida        \n";
    $stSQL .="     , (CASE WHEN pt.hora_entrada_2 is null                  \n";
    $stSQL .="               THEN ''                                       \n";
    $stSQL .="               ELSE to_char(pt.hora_entrada_2,'hh24:mi')     \n";
    $stSQL .="         end) as hora_entrada_2                              \n";
    $stSQL .="     , (CASE WHEN pt.hora_saida_2 is null                    \n";
    $stSQL .="               THEN ''                                       \n";
    $stSQL .="               ELSE to_char(pt.hora_saida_2,'hh24:mi')       \n";
    $stSQL .="         end) as hora_saida_2                                \n";
    $stSQL .="     , pt.cod_dia                                            \n";
    $stSQL .="     , dt.nom_dia                                            \n";
    $stSQL .="FROM                                                         \n";
    $stSQL .="  pessoal.faixa_turno as pt,        \n";
    $stSQL .="  (SELECT cod_grade                                          \n";
    $stSQL .="        , MAX(timestamp) as timestamp                        \n";
    $stSQL .="    FROM pessoal.faixa_turno        \n";
    $stSQL .="GROUP BY cod_grade) as pt_ult,                               \n";
    $stSQL .="  pessoal.dias_turno as dt          \n";
    $stSQL .="WHERE pt.cod_grade = pt_ult.cod_grade                        \n";
    $stSQL .="  AND pt.timestamp = pt_ult.timestamp                        \n";
    $stSQL .="  AND pt.cod_dia = dt.cod_dia                                \n";

    return $stSQL;
}

}
