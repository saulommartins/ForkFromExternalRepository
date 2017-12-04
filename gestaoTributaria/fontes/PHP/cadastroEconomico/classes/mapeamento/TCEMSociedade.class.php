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
  * Classe de mapeamento da tabela ECONOMICO.SOCIEDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMSociedade.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.7  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.SOCIEDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMSociedade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMSociedade()
{
    parent::Persistente();
    $this->setTabela('economico.sociedade');

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm,inscricao_economica,timestamp');

    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('quota_socio','numeric',true,'14,2',false,false);

}

function recuperaSociedadeInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSociedadeInscricao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSociedadeInscricao()
{
    $stSql .= "   SELECT                                                                                            \n";
    $stSql .= "       SO.NUMCGM,                                                                                    \n";
    $stSql .= "       SO.QUOTA_SOCIO,                                                                               \n";
    $stSql .= "       CG.NOM_CGM                                                                                    \n";
    $stSql .= "   FROM                                                                                              \n";
    $stSql .= "       (   SELECT  a.inscricao_economica, a.quota_socio, a.numcgm                                    \n";
    $stSql .= "           FROM    economico.sociedade a,                                                            \n";
    $stSql .= "                   (   SELECT max(\"timestamp\") as \"timestamp\", inscricao_economica               \n";
    $stSql .= "                       FROM economico.sociedade                                                      \n";
    $stSql .= "                       GROUP BY inscricao_economica) b                                               \n";
    $stSql .= "           WHERE a.inscricao_economica = b.inscricao_economica AND a.\"timestamp\" =b.\"timestamp\"  \n";
    $stSql .= "       )   AS SO,                                                                                    \n";
    $stSql .= "       sw_cgm          AS CG        \n";
    $stSql .= "   WHERE                            \n";
    $stSql .= "       SO.NUMCGM = CG.NUMCGM        \n";

    return $stSql;
}
}
