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
    * Classe de mapeamento da tabela TESOURARIA_FECHAMENTO
    * Data de Criação: 11/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32122 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.06
*/

/*
$Log$
Revision 1.10  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_FECHAMENTO
  * Data de Criação: 11/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaFechamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaFechamento()
{
    parent::Persistente();
    $this->setTabela("tesouraria.fechamento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_terminal, timestamp_terminal, timestamp_abertura, timestamp_fechamento, cgm_usuario, timestamp_usuario');

    $this->AddCampo('cod_terminal'          , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('timestamp_terminal'    , 'timestamp', true, ''  , true  , true  );
    $this->AddCampo('timestamp_abertura'    , 'timestamp', true, ''  , true  , true  );
    $this->AddCampo('timestamp_fechamento'  , 'timestamp',false, ''  , true  , false );
    $this->AddCampo('cod_boletim'           , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('cod_entidade'          , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('exercicio_boletim'     , 'varchar'  , true, '04', true  , true  );
    $this->AddCampo('cgm_usuario'           , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_usuario'     , 'timestamp', true, ''  , false , true  );
}

    public function recuperaMaxFechamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaMaxFechamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaMaxFechamento()
    {
        $stSql = "
            SELECT cod_terminal
                 , timestamp_terminal
                 , MAX(timestamp_abertura) AS timestamp_abertura
                 , MAX(timestamp_fechamento) AS timestamp_fechamento
                 , cod_boletim
                 , cod_entidade
                 , exercicio_boletim
                 , cgm_usuario
                 , timestamp_usuario
              FROM tesouraria.fechamento
             WHERE cod_terminal = ".$this->getDado('cod_terminal')."
               AND cod_entidade = ".$this->getDado('cod_entidade')."
          GROUP BY cod_terminal
                 , timestamp_terminal
                 , cod_boletim
                 , cod_entidade
                 , exercicio_boletim
                 , cgm_usuario
                 , timestamp_usuario
        ";

        return $stSql;

    }

}
