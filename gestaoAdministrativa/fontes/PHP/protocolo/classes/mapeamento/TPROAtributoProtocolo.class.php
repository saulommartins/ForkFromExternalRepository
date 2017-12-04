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
* Classe de Mapeamento para a tabela sw_atributo_protocolo
* Data de Criação: 01/09/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15582 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:38:09 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.06.93
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPROAtributoProtocolo extends Persistente
{
function TPROAtributoProtocolo()
{
    parent::Persistente();
    $this->setTabela('sw_atributo_protocolo');
    $this->setCampoCod('cod_atributo');

    $this->AddCampo('cod_atributo',	'integer',	true,'',	true,false);
    $this->AddCampo('nom_atributo',	'varcahar',	true,'60',	false,false);
    $this->AddCampo('tipo',			'char',		true,'1',	false,false);
    $this->AddCampo('valor_padrao',	'text',		true,'',	false,false);
}

public function recuperaAtributoAssunto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAtributoAssunto().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

public function montaRecuperaAtributoAssunto()
    {
        $stSql  = "
                    SELECT
                            AP.cod_atributo,
                            AP.nom_atributo,
                            AP.tipo,
                            AP.valor_padrao
                      FROM
                            sw_atributo_protocolo AS AP,
                            sw_assunto_atributo   AS AT
                      WHERE
                            AP.cod_atributo      = AT.cod_atributo AND
                            AT.cod_classificacao = ".$this->getDado('cod_classificacao')." AND
                            AT.cod_assunto       = ".$this->getDado('cod_assunto')."
                      ORDER BY
                            AP.nom_atributo
                ";
        return $stSql;
    }

public function recuperaAtributoValor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAtributoValor().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

public function montaRecuperaAtributoValor()
    {
        $stSql  = "
                    SELECT  AP.nom_atributo,
                            AP.cod_atributo,
                            AP.tipo,
                            AAV.valor
                      FROM  sw_assunto_atributo_valor AS AAV,
                            sw_atributo_protocolo AS AP
                      WHERE AAV.cod_processo      = ".$this->getDado('cod_processo')."      AND
                            AAV.exercicio         = '".$this->getDado('ano_exercicio')."'   AND
                            AAV.cod_assunto       = ".$this->getDado('cod_assunto')."       AND
                            AAV.cod_classificacao = ".$this->getDado('cod_classificacao')." AND
                            AAV.cod_atributo      = AP.cod_atributo
                      ORDER BY  AP.nom_atributo
                ";
        return $stSql;
    }

}
