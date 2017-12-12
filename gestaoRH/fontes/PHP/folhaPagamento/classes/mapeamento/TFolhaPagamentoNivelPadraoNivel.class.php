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
* Classe de mapeamento da tabela FOLHAPAGAMENTO.NIVEL_PADRAO_NIVEL
* Data de Criação   : 05/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.NIVEL_PADRAO_NIVEL
  * Data de Criação: 05/10/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoNivelPadraoNivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TFolhaPagamentoNivelPadraoNivel()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.nivel_padrao_nivel');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_nivel_padrao,cod_padrao,timestamp');

        $this->AddCampo('cod_nivel_padrao', 'integer',   true, '',      true, true);
        $this->AddCampo('cod_padrao',       'integer',   true, '',      true, true);
        $this->AddCampo('descricao',        'varchar',   true, '80',   false, false);
        $this->AddCampo('valor',            'numeric',   true, '14.2', false, false);
        $this->AddCampo('percentual',       'numeric',   true, '5.2',  false, false);
        $this->AddCampo('qtdmeses',         'integer',   true, '',     false, false);
        $this->AddCampo('timestamp',        'timestamp', false, '',     true, false);
    }

    public function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL  = " SELECT                                                  \n";
        $stSQL .= "    FPNP.cod_nivel_padrao,                               \n";
        $stSQL .= "    FPNP.cod_padrao,                                     \n";
        $stSQL .= "    FPNP.descricao,                                      \n";
        $stSQL .= "    FPNP.valor,                                          \n";
        $stSQL .= "    FPNP.percentual,                                     \n";
        $stSQL .= "    FPNP.qtdmeses,                                       \n";
        $stSQL .= "    FPNP.timestamp                                       \n";
        $stSQL .= " FROM                                                    \n";
        $stSQL .= "    folhapagamento.nivel_padrao_nivel FPNP,          \n";
        $stSQL .= "   (SELECT                                               \n";
        $stSQL .= "       FPP.cod_padrao,                                   \n";
        $stSQL .= "       max(FPP.timestamp) as timestamp_padrao            \n";
        $stSQL .= "    FROM folhapagamento.padrao_padrao FPP            \n";
        $stSQL .= "    GROUP BY FPP.cod_padrao                              \n";
        $stSQL .= "   ) as MAX_FPP                                          \n";
        $stSQL .= " WHERE FPNP.cod_padrao       = MAX_FPP.cod_padrao        \n";
        $stSQL .= " AND   FPNP.timestamp        = MAX_FPP.timestamp_padrao  \n";

        return $stSQL;
    }
}
