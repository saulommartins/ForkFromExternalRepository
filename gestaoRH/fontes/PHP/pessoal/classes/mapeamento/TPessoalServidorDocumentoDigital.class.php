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
  * Classe de mapeamento da tabela PESSOAL.SERVIDOR_DOCUMENTO_DIGITAL
  * Data de Criação: 05/07/2016

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Michel Teixeira

  * @package URBEM
  * @subpackage Mapeamento

  $Id: TPessoalServidorDocumentoDigital.class.php 66020 2016-07-07 18:08:18Z michel $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TPessoalServidorDocumentoDigital extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela('pessoal.servidor_documento_digital');

        $this->setCampoCod('cod_servidor');
        $this->setComplementoChave('cod_tipo');

        $this->AddCampo('cod_servidor'   , 'integer', true,   '' ,  true, true);
        $this->AddCampo('cod_tipo'       , 'integer', true,   '' ,  true, true);
        $this->AddCampo('nome_arquivo'   , 'varchar', true, '100', false, false);
        $this->AddCampo('arquivo_digital', 'varchar', true, '250', false, false);
    }

    function recuperaServidorDocumentoDigital(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = $stOrdem ? $stOrdem : " ORDER BY tipo_documento_digital.descricao ";
        $stSql  = $this->montaRecuperaServidorDocumentoDigital().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaServidorDocumentoDigital()
    {
        $stSQL = "
                SELECT servidor_documento_digital.*
                     , tipo_documento_digital.descricao
                  FROM pessoal.servidor_documento_digital
            INNER JOIN pessoal.tipo_documento_digital
                    ON tipo_documento_digital.cod_tipo = servidor_documento_digital.cod_tipo
                 WHERE cod_servidor = ".$this->getDado('cod_servidor');

        return $stSQL;
    }
}
