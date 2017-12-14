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
  * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_GERADO_ARQUIVO_DIGITAL
  * Data de Criação: 28/07/2016

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Michel Teixeira

  * @package URBEM
  * @subpackage Mapeamento

  $Id: TPessoalAssentamentoArquivoDigital.class.php 66301 2016-08-05 13:36:34Z michel $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TPessoalAssentamentoArquivoDigital extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela('pessoal.assentamento_gerado_arquivo_digital');

        $this->setCampoCod('cod_assentamento_gerado');
        $this->setComplementoChave('nome_arquivo');

        $this->AddCampo('cod_assentamento_gerado' , 'integer', true,   '' ,  true,  true);
        $this->AddCampo('nome_arquivo'            , 'varchar', true, '100',  true, false);
        $this->AddCampo('arquivo_digital'         , 'varchar', true, '250', false, false);
    }

    function recuperaAssentamentoArquivoDigital(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = $stOrdem ? $stOrdem : " ORDER BY assentamento_gerado_arquivo_digital.nome_arquivo ";
        $stSql  = $this->montaRecuperaAssentamentoArquivoDigital().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaAssentamentoArquivoDigital()
    {
        $stSQL = "
                SELECT assentamento_gerado_arquivo_digital.*
                     , assentamento_gerado_contrato_servidor.cod_contrato
                     , assentamento_gerado.cod_assentamento
                     , TO_CHAR(assentamento_gerado.periodo_inicial,'dd/mm/yyyy') AS periodo_inicial
                     , TO_CHAR(assentamento_gerado.periodo_final,'dd/mm/yyyy') AS periodo_final
                  FROM pessoal.assentamento_gerado_arquivo_digital
            INNER JOIN pessoal.assentamento_gerado
                    ON assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_arquivo_digital.cod_assentamento_gerado
                   AND assentamento_gerado.timestamp = ( select max(timestamp)
                                                           from pessoal.assentamento_gerado as ag
                                                          where ag.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                                       )
            INNER JOIN pessoal.assentamento_gerado_contrato_servidor
                    ON assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                 WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = ".$this->getDado('cod_assentamento_gerado')."
                   AND assentamento_gerado_contrato_servidor.cod_contrato = ".$this->getDado('cod_contrato')."
                ";

        return $stSQL;
    }
}
