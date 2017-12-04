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
    * Classe de mapeamento da tabela TCMPA.TIPO_CARGO
    * Data de Criação: 16/01/2008

    * @author Analista: Gelson W. Golçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  TCMPA.TIPO_UNIDADE_GESTORA
  * Data de Criação: 16/01/2008

  * @author Analista: Gelson W. Golçalves
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/
class TTPATipoCargo extends Persistente
{

    /**
      * Método Construtor
      * @access Private
    */
    public function TTPATipoCargo()
    {
        parent::Persistente();
        $this->setTabela('tcmpa'.Sessao::getEntidade().'.tipo_cargo');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_cargo');

        $this->AddCampo( 'descricao', 'integer', true, '', true , false );
        $this->AddCampo( 'cod_tipo'     , 'integer', true, '', false, false );
    }

    public function recuperaDescricaoTiposCargo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDescricaoTiposCargo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDescricaoTiposCargo()
    {
        $stSQL .= " SELECT descricao
                      FROM tcmpa".Sessao::getEntidade().".tipo_cargo";

        return $stSQL;
    }

    public function recuperaDadosFuncionariosAgentesPoliticos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosFuncionariosAgentesPoliticos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosFuncionariosAgentesPoliticos()
    {
        $stSql .= "SELECT '040' AS tipo_registro\n";
        $stSql .= "     , contrato.registro AS identificador\n";
        $stSql .= "     , contrato.cod_contrato     \n";
        $stSql .= "     , sw_cgm_pessoa_fisica.cpf\n";
        $stSql .= "     , translate(sw_cgm_pessoa_fisica.servidor_pis_pasep, '.-','') AS pis_pasep     \n";
        $stSql .= "     , (CASE WHEN UPPER(sw_cgm_pessoa_fisica.sexo) = 'M' THEN 1 ELSE 2 END) AS sexo     \n";
        $stSql .= "     , to_char(sw_cgm_pessoa_fisica.dt_nascimento, 'ddmmyyyy') AS dt_nascimento     \n";
        $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE servidor.numcgm = numcgm) AS nome\n";
        $stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_admissao, 'ddmmyyyy') AS admissao\n";
        $stSql .= "     , (select to_char(dt_rescisao, 'ddmmyyyy') from pessoal".Sessao::getEntidade().".contrato_servidor_caso_causa where contrato_servidor.cod_contrato = cod_contrato) as demissao \n";
        $stSql .= "     , '*' AS fim_registro\n";
        $stSql .= "  FROM pessoal".Sessao::getEntidade().".contrato_servidor_orgao\n";
        $stSql .= "     , (SELECT cod_contrato\n";
        $stSql .= "             , MAX(timestamp) as timestamp\n";
        $stSql .= "          FROM pessoal".Sessao::getEntidade().".contrato_servidor_orgao\n";
        $stSql .= "         GROUP BY cod_contrato) AS max_contrato_servidor_orgao\n";
        $stSql .= "     , pessoal".Sessao::getEntidade().".contrato\n";
        $stSql .= "     , pessoal".Sessao::getEntidade().".contrato_servidor\n";
        $stSql .= "     , pessoal".Sessao::getEntidade().".servidor_contrato_servidor\n";
        $stSql .= "     , pessoal".Sessao::getEntidade().".servidor\n";
        $stSql .= "     , pessoal".Sessao::getEntidade().".contrato_servidor_nomeacao_posse\n";
        $stSql .= "     , ( SELECT cod_contrato\n";
        $stSql .= "              , max(timestamp) as timestamp\n";
        $stSql .= "           FROM pessoal".Sessao::getEntidade().".contrato_servidor_nomeacao_posse\n";
        $stSql .= "          GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse\n";
        $stSql .= "      , sw_cgm_pessoa_fisica\n";
        $stSql .= " WHERE contrato_servidor.cod_contrato                = contrato.cod_contrato\n";
        $stSql .= "   AND contrato_servidor.cod_contrato                = servidor_contrato_servidor.cod_contrato\n";
        $stSql .= "   AND servidor_contrato_servidor.cod_servidor       = servidor.cod_servidor\n";
        $stSql .= "   AND servidor.numcgm                               = sw_cgm_pessoa_fisica.numcgm\n";
        $stSql .= "   AND contrato_servidor.cod_contrato                = contrato_servidor_nomeacao_posse.cod_contrato\n";
        $stSql .= "   AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato\n";
        $stSql .= "   AND contrato_servidor_nomeacao_posse.timestamp    = max_contrato_servidor_nomeacao_posse.timestamp\n";
        $stSql .= "   AND contrato.cod_contrato                         = contrato_servidor_orgao.cod_contrato\n";
        $stSql .= "   AND contrato_servidor_orgao.cod_contrato          = max_contrato_servidor_orgao.cod_contrato\n";
        $stSql .= "   AND contrato_servidor_orgao.timestamp             = max_contrato_servidor_orgao.timestamp\n";
        $stSql .= "   AND EXISTS (SELECT 1\n";
        $stSql .= "                 FROM folhapagamento".Sessao::getEntidade().".registro_evento_periodo\n";
        $stSql .= "                    , folhapagamento".Sessao::getEntidade().".evento_calculado\n";
        $stSql .= "                    , folhapagamento".Sessao::getEntidade().".periodo_movimentacao\n";
        $stSql .= "                WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro\n";
        $stSql .= "                  AND registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao\n";
        $stSql .= "                  AND to_char(periodo_movimentacao.dt_final, 'yyyymm') BETWEEN ".$this->getDado('dt_inicial')." AND ".$this->getDado('dt_final')." \n";
        $stSql .= "                  AND registro_evento_periodo.cod_contrato = contrato.cod_contrato)\n";
        $stSql .= "   AND contrato_servidor.cod_cargo                   = ".$this->getDado('cod_cargo')."\n";
        $stSql .= "   AND contrato_servidor.cod_regime                  = ".$this->getDado('cod_regime')."\n";
        $stSql .= "   AND contrato_servidor.cod_sub_divisao             = ".$this->getDado('cod_sub_divisao')."\n";
        $stSql .= "   AND contrato_servidor_orgao.cod_orgao             = ".$this->getDado('cod_orgao')."\n";
        $stSql .= "   ORDER BY nome  \n";

        return $stSql;
    }

    public function recuperaListagemSubDivisao(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaListagemSubDivisao", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaListagemSubDivisao()
    {
        $stSql  = "\n SELECT sub_divisao.cod_regime
                           , sub_divisao.cod_sub_divisao
                           , sub_divisao.descricao
                        FROM pessoal".Sessao::getEntidade().".sub_divisao
                       WHERE sub_divisao.cod_regime = ".$this->getDado('cod_regime')."
                       ORDER BY cod_sub_divisao";

        return $stSql;
    }
}
