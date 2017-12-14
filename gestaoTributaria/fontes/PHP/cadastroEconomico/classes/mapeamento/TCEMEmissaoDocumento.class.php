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
  * Classe de mapeamento da tabela ECONOMICO.EMISSAO_DOCUMENTO
  * Data de Criação: 09/10/2006

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMEmissaoDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.3  2007/03/02 14:51:41  dibueno
Bug #7676#

Revision 1.2  2006/10/11 17:42:55  dibueno
Adição de função para buscar ultimo registro de emissão

Revision 1.1  2006/10/11 10:29:18  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCEMEmissaoDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCEMEmissaoDocumento()
    {
        parent::Persistente();
        $this->setTabela('economico.emissao_documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_licenca, exercicio');

        $this->AddCampo('cod_licenca','integer',true,'',true,true);
        $this->AddCampo('exercicio','char',true,'4',true,true);
        $this->AddCampo('cod_tipo_documento','integer',true,'',true,true);
        $this->AddCampo('cod_documento','integer',true,'',true,true);

        $this->AddCampo('num_emissao','integer',true,'',true,false);
        $this->AddCampo('numcgm_diretor','integer',true,'',false,false);
        $this->AddCampo('numcgm_usuario','integer',true,'',false,false);

        $this->AddCampo('timestamp','timestamp',false,'',true,false);
        $this->AddCampo('dt_emissao','date',true,'',false,false);

    }

    public function recuperaUltimoRegistro(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaUltimoRegistro().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaUltimoRegistro()
    {
        $stSql = "
            SELECT
                coalesce ( max(num_emissao), 0 ) as valor
            FROM
                economico.emissao_documento
        ";

        return $stSql;
    }

}
