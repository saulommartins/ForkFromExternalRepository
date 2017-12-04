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
    * Regra para Arquivo Coletora
    *
    *
    * @date 11/08/2010
    * @author Analista: Gelson
    * @author Desenvol: Tonismar
    *
    * @ignore
**/
class RPatrimonioArquivoColetora
{
    public $transacao;
    public $nome;
    public $md5sum;
    public $local;
    public $placa;
    public $path;
    public $codigo=0;
    public $status;

    public function setMd5sum($valor)
    {
        $this->md5sum = md5_file($valor);
    }

    public function RPatrimonioArquivoColetora()
    {
        $this->transacao = new Transacao;
    }

    public function incluirArquivo($trans='')
    {
        include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetora.class.php' );
        $model = new TPatrimonioArquivoColetora;
        $model->proximoCod($this->codigo);
        $model->setDado( 'nome', $this->nome );
        $model->setDado( 'md5sum', $this->md5sum );
        $model->setDado( 'codigo', $this->codigo );
        $e = $model->inclusao( $trans );

        return $e;
    }

    public function incluirArquivoDados($trans='')
    {
       include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraDados.class.php' );
       $model = new TPatrimonioArquivoColetoraDados;
       $model->setDado( 'cod_local', $this->local );
       $model->setDado( 'num_placa', $this->placa );
       $model->setDado( 'codigo'   , $this->codigo);

       $e = $model->inclusao( $trans );

       return $e;
    }

    /**
    **/
    public function incluirConsistencia($trans='')
    {
        include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraConsistencia.class.php' );
        $model = new TPatrimonioArquivoColetoraConsistencia;
        $model->setDado( 'codigo', $this->codigo );
        $model->setDado( 'num_placa', $this->placa );
        $model->setDado( 'status', $this->status );
        $model->setDado( 'orientacao', $this->orientacao );
        $e = $model->inclusao( $trans );

        return $e;
    }

    /**
    * @description Remove todos os registros referente ao arquivo $codigo já cadastrados
    * quando ocorre algum erro.
    **/
    public function rollback($codigo, $trans='')
    {
        $flagTransacao = false;
        $e = $this->transacao->abreTransacao( $flagTransacao, $trans );
        if ( !$e->ocorreu() ) {
            include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetora.class.php' );
            include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraDados.class.php' );
            include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraConsistencia.class.php' );
            $modelConsistencia = new TPatrimonioArquivoColetoraConsistencia;
            $modelConsistencia->setDado( 'codigo', $this->codigo );
            $e = $modelConsistencia->exclusao( $trans );
            if ( !$e->ocorreu() ) {
                $modelDado = new TPatrimonioArquivoColetoraDados;
                $modelDado->setDado( 'codigo', $this->codigo );
                $e = $modelDado->exclusao( $trans );
                if ( !$e->ocorreu() ) {
                    $modelArquivo = new TPatrimonioArquivoColetora;
                    $modelArquivo->setDado( 'codigo', $this->codigo );
                    $modelArquivo->exclusao( $trans );
                }
            }
        }
        $this->transacao->fechaTransacao( $flagTransacao, $trans, $e, $modelArquivo );

        return $e;
    }

    public function importar($arquivo, $trans='')
    {
        $flagTransacao = false;
        $e = $this->transacao->abreTransacao( $flagTransacao, $trans );
        if ( !$e->ocorreu() ) {
            if ( is_array($arquivo) ) {
                $e = $this->validateFileName();
                if ( !$e->ocorreu() ) {
                    $e = $this->checkFileName();
                    if ( !$e->ocorreu() ) {
                       $e = $this->checkFileContent();
                        if ( !$e->ocorreu() ) {
                            $e = $this->incluirArquivo( $trans );
                            if ( !$e->ocorreu() ) {
                                foreach ($arquivo as $linha) {
                                    if ( !$e->ocorreu() ) {
                                        $this->local = $linha['cod_local'];
                                        $this->placa = $linha['num_placa'];
                                        $e = $this->incluirArquivoDados( $trans );
                                         if ( !$e->ocorreu() ) {
                                            $e = $this->consistirArquivo( $trans );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ( $e->ocorreu() ) {
            if (is_array($linha)) {
               $descricao = ('Local inexistente ou número de placa coletada duplicada. ');
            } else {
                $descricao = $e->getDescricao();
            }
            $e = $this->rollback( $this->codigo );

            $e->setDescricao( $descricao );
        }
        $this->transacao->fechaTransacao( $flagTransacao, $trans, $e );

        return $e;
    }

    /**
    * @description Private - Verifica se o nome do arquivo está na forma correta: inventario_YYYYMMDDHHMM.txt
    * @todo Aplicar a verificação de acordo com uma configuração prévia e não fixa de nome de arquivo.
    **/
    public function validateFileName()
    {
        $e = new Erro;
        $regex = '/^coleta_([1-2][0,9][0-9]{2})(0[0-9]|1[0-2])([0-2][0-9]|3[0-1])([0-1][0-9]|2[0-3])([0-5][0-9])\.txt$/';
        if ( !preg_match($regex, $this->nome ) ) {
            $e->setDescricao('Nome do arquivo não está no padrão (coleta_YYYYMMDDHHMM.txt)');
        }

        return $e;
    }

    /**
    * @description Private - Verifica se o conteúdo do arquivo, através do checksum, se já foi importado
    **/
    private function checkFileContent()
    {
        include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetora.class.php');
        $model = new TPatrimonioArquivoColetora;
        $model->setDado('md5sum', $this->md5sum );
        $e = $model->recuperaMd5sum($lista);
        if ( !$e->ocorreu() ) {
            if ( $lista->getNumLinhas() > 0 ) {
                $e->setDescricao('O conteúdo desse arquivo já foi importado.');
            }
        }

        return $e;
    }

    /**
    * @description Verifica se já não existe o nome de arquivo cadastrado.
    **/
    private function checkFileName()
    {
        include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetora.class.php');
        $model = new TPatrimonioArquivoColetora;
        $model->setDado('nome', $this->nome );
        $e = $model->recuperaNomeArquivo($lista);
        if ( !$e->ocorreu() ) {
            if ( $lista->getNumLinhas() > 0 ) {
                $e->setDescricao('Nome de arquivo já cadastrado.');
            }
        }

        return $e;
    }

    /**
    **/
    public function listarArquivosLocal(&$lista)
    {
        include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetora.class.php' );
        $model = new TPatrimonioArquivoColetora;
        $model->setDado( 'codigo', $this->codigo );
        $e = $model->recuperaArquivosLocal( $lista, $filtro, $trans='' );

        return $e;
    }

    /**
    * @description Valida o arquivo de acordo com as condições das informações lidas
    **/
    public function consistirArquivo($trans='')
    {
        $e = $this->consultaPlaca( $lista );
        if ( !$e->ocorreu() ) {
            if ( $lista->getNumLinhas() >= 0 ) {
                if ( $this->local == $lista->getCampo( 'cod_local' ) ) {
                    $this->status = 'Sem divergência';
                    $this->orientacao = '';
                } else {
                    $this->status = 'Divergente';
                    $this->orientacao = 'Local informado no sistema: '.$lista->getCampo('cod_local');
                    include_once(CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraDados.class.php');
                    $model = new TPatrimonioArquivoColetoraDados;
                    $model->setDado('cod_local', $this->local);
                    $model->setDado('codigo', $this->codigo );
                    $model->setDado('num_placa', $this->placa );
                    $e = $model->alteracao();
                }
            } else {
                $this->status = 'Não cadastrado';
                $this->orientacao = 'Cadastrar no Urbem';
            }
            $e = $this->incluirConsistencia( $trans );
        }

        return $e;
    }

    /**
    * @description Busca pelos num_placa do Urbem não lidos via coletora
    * @params $lista
    **/
    private function consultaPlacaUrbem(&$lista)
    {
        include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraDados.class.php' );
        $model = new TPatrimonioArquivoColetoraDados;
        $e = $model->recuperaPlacaUrbem( $lista );

        return $e;
    }

    /**
    * @description Verifica a situação de um num_placa lido via coletora
    * @params $lista
    **/
    private function consultaPlaca(&$lista)
    {
        include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraDados.class.php' );
        $model = new TPatrimonioArquivoColetoraDados;
        $model->setDado( 'num_placa', $this->placa );
        $e = $model->recuperaPlaca( $lista );

        return $e;
    }

}
