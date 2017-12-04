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
  * Classe de mapeamento da tabela FOLHAPAGAMENTO.SALARIO_FAMILIA_EVENTO
  * Data de Criação: 19/04/2006

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.05.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela FOLHAPAGAMENTO.SALARIO_FAMILIA_EVENTO
  * Data de Criação: 19/04/2006

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoSalarioFamiliaEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TFolhaPagamentoSalarioFamiliaEvento()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.salario_familia_evento');

        $this->setCampoCod('cod_regime_previdencia');
        $this->setComplementoChave('');

        $this->AddCampo('cod_regime_previdencia', 'integer'  , true, '',  true,  true );
        $this->AddCampo('timestamp'             , 'timestamp', true, '',  true,  true );
        $this->AddCampo('cod_tipo'              , 'integer'  , true, '',  true,  true );
        $this->AddCampo('cod_evento'            , 'integer'  , true, '', false,  true );
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = " SELECT fsfe.cod_regime_previdencia                                     					\n";
        $stSql .= "      , TO_CHAR(fsfe.timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp 					\n";
        $stSql .= "      , fsfe.cod_tipo                                                   					\n";
        $stSql .= "      , fsfe.cod_evento                                                 					\n";
        $stSql .= "      , fe.codigo                                                       					\n";
        $stSql .= "      , fe.descricao as descricao_evento                                					\n";
        $stSql .= "   FROM folhapagamento.salario_familia_evento fsfe                      					\n";
        $stSql .= "   JOIN ( SELECT cod_tipo											   					\n";
        $stSql .= "		          , cod_regime_previdencia								   					\n";
        $stSql .= "			      , max(timestamp) as timestamp							   					\n";
        $stSql .= "    	       FROM folhapagamento.salario_familia_evento				   					\n";
        $stSql .= "	       GROUP BY cod_tipo											   					\n";
        $stSql .= "		          , cod_regime_previdencia) as max_salario_familia_evento  					\n";
        $stSql .= "	    ON max_salario_familia_evento.cod_tipo = fsfe.cod_tipo			   					\n";
        $stSql .= "	   AND max_salario_familia_evento.cod_regime_previdencia = fsfe.cod_regime_previdencia 	\n";
        $stSql .= "	   AND max_salario_familia_evento.timestamp = fsfe.timestamp		   					\n";
        $stSql .= "   JOIN folhapagamento.evento fe                                        					\n";
        $stSql .= "     ON fe.cod_evento = fsfe.cod_evento                                 					\n";

        return $stSql;
    }

}
