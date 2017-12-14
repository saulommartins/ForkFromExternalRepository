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
 * Classe adaptadora de Persistente
 * Data de Criação: 19/11/2008

 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>

 * @package URBEM
 * @subpackage Mapeamento

 * $Id: $

 */
class PersistenteAdapter extends Persistente
{

    /**
     * Método construtor
     *
     */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
     * Encapsula método "inclusao" de Persistente
     *
     */
    public function inclusao()
    {
        return self::executarMetodoPersistente('inclusao', true);
    }

    /**
     * Encapsula método "alteracao" de Persistente
     */
    public function alteracao()
    {
        return self::executarMetodoPersistente('alteracao', false);
    }

    /**
     * Encapsula método "exclusao" de Persistente
     */
    public function exclusao()
    {
        return self::executarMetodoPersistente('exclusao', false);
    }

    /**
     * Executa um método da Persistente
     *
     * @param  string $stMetodo
     * @param  bool   $boChecarSerial
     * @return mixed
     */
    private function executarMetodoPersistente($stMetodo, $boChecarSerial)
    {
        $arEstruturaOriginal = $this->arEstrutura;
        $this->checarSerial($boChecarSerial);
        $rs = parent::$stMetodo();
        $this->arEstrutura = $arEstruturaOriginal;

        return $rs;
    }

    /**
     * Utilizado pelo método exclusao.
     * Verifica a existência de campo do tipo serial:
     * Se for inclusão, o campo serial é excluído de arEstrutura;
     * Se alteração ou exclusão, o serial é mantido e seu tipo é
     * alterado para 'integer', como é esperado pela Persistente.
     * O campo deverá estar mapeado como "serial".
     *
     * @param  bool $boInclusao
     * @return void
     */
    private function checarSerial($boInclusao)
    {
        $reordenar = false;
        $i = 0;
        foreach ($this->arEstrutura as $item) {
            if ($item->stTipoCampo == 'serial') {
                if ($boInclusao) {
                    // Retira atributo serial
                    unset($this->arEstrutura[$i]);
                    $reordenar = true;
                } else {
                    // Mantem serial e altera para tipo correto
                    $item->stTipoCampo = 'integer';
                }
            }
            $i++;
        }
        if ($reordenar) {
            // Item serial foi excluído, reordenar os índices do array
            $this->arEstrutura = array_values($this->arEstrutura);
        }
    }

    /**
     * Busca o último valor do campo tipo serial
     */
    public function getUltimoSerial(&$inValor, $boTransacao)
    {
        return $this->getUltimoValor('serial', $inValor, $boTransacao);
    }

    /**
     * Busca o último valor do campo tipo timestamp
     */
    public function getUltimoTimestamp(&$inValor, $boTransacao)
    {
        return $this->getUltimoValor('timestamp', $inValor, $boTransacao);
    }

    /**
     * Recupera o último valor gerado pelo banco para o campo.
     *
     * @param  string $tipoCampo   Ex: serial, timestamp, etc
     * @param  bool   $boTransacao
     * @return int    Último código serial da tabela
     *
     * @todo Verificar qual atributo armazena o nome do campo
     * @todo Verificar qual método retorna o nome da tabela
     */
    private function getUltimoValor($tipoCampo, &$inValor, $boTransacao)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $boCampoEncontrado = false;
        // Buscar na estrutura qual é o nome do campo serial
        foreach ($this->arEstrutura as $item) {
            if ($item->stTipoCampo == $tipoCampo) {
                $stSql = 'SELECT MAX('.$item->stNomeCampo.') AS ultimo_valor
                            FROM ' . $this->getTabela();
                $this->setDebug( $stSql );
                $boCampoEncontrado = true;
                break;
            }
        }
        if ($boCampoEncontrado) {
            $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

            if ( !$obErro->ocorreu() ) {
                $inValor = $rsRecordSet->getCampo("ultimo_valor");

            }
        } else {
            $obErro->setDescricao('Campo tipo "'.$tipoCampo.'" não mapeado!');
        }

        return $obErro;
    }

}
?>
