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
  * Classe de mapeamento da tabela ECONOMICO.DIAS_CADASTRO_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMDiasCadastroEconomico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.8  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.DIAS_CADASTRO_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMDiasCadastroEconomico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMDiasCadastroEconomico()
{
    parent::Persistente();
    $this->setTabela('economico.dias_cadastro_economico');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_dia,inscricao_economica,timestamp');

    $this->AddCampo('cod_dia'            ,'integer'  ,true,'',true,true);
    $this->AddCampo('inscricao_economica','integer'  ,true,'',true,true);
    $this->AddCampo('timestamp'          ,'timestamp',false,'',true,false);
    $this->AddCampo('hr_inicio'          ,'time'     ,true,'',false,false);
    $this->AddCampo('hr_termino'         ,'time'     ,true,'',false,false);

}

function recuperaEmpresaHorarios(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaEmpresaHorarios( $stFiltro ).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaEmpresaHorarios($stFiltro)
{
    $stSql .=  " SELECT                                                                 \n";
    $stSql .=  "    d.cod_dia,                                                          \n";
    $stSql .=  "    d.inscricao_economica,                                              \n";
    $stSql .=  "    d.hr_inicio,                                                        \n";
    $stSql .=  "    d.hr_termino,                                                       \n";
    $stSql .=  "    d.timestamp,                                                        \n";
    $stSql .=  "    s.nom_dia                                                           \n";
    $stSql .=  " FROM                                                                   \n";
    $stSql .=  "    economico.cadastro_economico as c,                                  \n";
    $stSql .=  "    economico.dias_cadastro_economico as d,                             \n";
    $stSql .=  "    administracao.dias_semana as s,                                     \n";
    $stSql .=  "    ( SELECT                                                            \n";
    $stSql .=  "            m.inscricao_economica,
                            max(m.timestamp) as timestamp        \n";
    $stSql .=  "           /* m.cod_dia, m.hr_inicio, m.hr_termino */                   \n";
    $stSql .=  "      FROM                                                              \n";
    $stSql .=  "            economico.dias_cadastro_economico as m                      \n";
    $stSql .=  "      GROUP BY                                                          \n";
    $stSql .=  "            m.inscricao_economica                                       \n";
    $stSql .=  "            /*m.cod_dia,                                                \n";
    $stSql .=  "            m.hr_inicio,                                                \n";
    $stSql .=  "            m.hr_termino    */                                          \n";
    $stSql .=  "    ) n                                                                 \n";
    $stSql .=  "WHERE                                                                   \n";
    $stSql .=  "    d.inscricao_economica = n.inscricao_economica and                   \n";
    $stSql .=  "    d.timestamp           = n.timestamp           and                   \n";
    $stSql .=  "    c.inscricao_economica = d.inscricao_economica and                   \n";
    $stSql .=  "    d.cod_dia             = s.cod_dia                                   \n";
    $stSql .=  "    ".$stFiltro."                                                       \n";
    $stSql .=  "GROUP BY                                                                \n";
    $stSql .=  "    d.cod_dia,                                                          \n";
    $stSql .=  "    d.inscricao_economica,                                              \n";
    $stSql .=  "    d.hr_inicio,                                                        \n";
    $stSql .=  "    d.hr_termino,                                                       \n";
    $stSql .=  "    s.nom_dia,                                                          \n";
    $stSql .=  "    d.timestamp                                                         \n";

    return $stSql;
}

}
